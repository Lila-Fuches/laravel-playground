<?php

namespace Support;

use Ramsey\Uuid\Uuid;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Builder;
use BadMethodCallException;
use Illuminate\Http\Request;
use Lcobucci\JWT\Signer\Key;
use Illuminate\Support\Carbon;
use Lcobucci\JWT\ValidationData;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\Guard;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Auth\Events\OtherDeviceLogout;
use Illuminate\Contracts\Auth\Authenticatable;

class JwtGuard implements Guard
{
    use GuardHelpers;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var \Lcobucci\JWT\Token|null
     */
    private Token $token;

    /**
     * @var array
     */
    private array $config;

    /**
     * @var \Illuminate\Http\Request|null
     */
    private Request $request;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected Dispatcher $events;

    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable|null
     */
    private Authenticatable $lastAttempted;

    public function __construct(UserProvider $userProvider, string $name, array $config = [])
    {
        $this->setProvider($userProvider);
        $this->name   = $name;
        $this->config = $config;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user():? Authenticatable
    {
        if ($this->user === null) {
            $token = $this->getTokenFromRequest();
            if ($token !== null) {
                $this->user = $this->getProvider()->retrieveById($token->getClaim('sub'));
            }
        }
        return $this->user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        return $this->getProvider()->retrieveByCredentials($credentials) !== null;
    }

    /**
     * @return \Lcobucci\JWT\Token|null
     */
    public function token():? Token
    {
        return $this->token;
    }

    /**
     * Get the current request instance.
     *
     * @return \Illuminate\Http\Request
     */
    public function getRequest(): Request
    {
        return $this->request ?: Request::createFromGlobals();
    }

    /**
     * Set the current request instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param array $credentials
     *
     * @return \Lcobucci\JWT\Token|null
     * @throws \Exception
     */
    public function attempt(array $credentials = []): ?Token
    {
        $this->fireAttemptEvent($credentials, false);
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);
        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if ($this->hasValidCredentials($user, $credentials)) {
            return $this->login($user);
        }
        // If the authentication attempt fails we will fire an event so that the user
        // may be notified of any suspicious attempts to access their account from
        // an unrecognized user. A developer may listen to this event as needed.
        $this->fireFailedEvent($user, $credentials);
        return null;
    }

    /**
     * Log a user into the application.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return \Lcobucci\JWT\Token
     * @throws \Exception
     */
    public function login(Authenticatable $user): Token
    {
        $time  = Carbon::now();
        $token = (new Builder)
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy(Uuid::uuid4())
            ->issuedAt($time->timestamp)
            ->expiresAt($time->addMonth()->timestamp)
            ->relatedTo($user->getAuthIdentifier())
            ->getToken(new Sha256, new Key($this->config['key']));
        // If we have an event dispatcher instance set we will fire an event so that
        // any listeners will hook into the authentication events and run actions
        // based on the login and logout events fired from the guard instances.
        $this->fireLoginEvent($user, false);
        $this->setUser($user)->setToken($token);

        return $token;
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher
     */
    public function getDispatcher(): Dispatcher
    {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return $this
     */
    public function setDispatcher(Dispatcher $events): self
    {
        $this->events = $events;
        return $this;
    }

    /**
     * Get the last user we attempted to authenticate.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getLastAttempted()
    {
        return $this->lastAttempted;
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param mixed $user
     * @param array $credentials
     *
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials): bool
    {
        return $user !== null && $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * Fire the attempt event with the arguments.
     *
     * @param array $credentials
     * @param bool  $remember
     *
     * @return void
     */
    protected function fireAttemptEvent(array $credentials, $remember = false)
    {
        if (isset($this->events)) {
            $this->events->dispatch(new Attempting(
                $this->name,
                $credentials,
                $remember
            ));
        }
    }

    /**
     * Fire the login event if the dispatcher is set.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param bool                                       $remember
     *
     * @return void
     */
    protected function fireLoginEvent($user, $remember = false)
    {
        if (isset($this->events)) {
            $this->events->dispatch(new Login(
                $this->name,
                $user,
                $remember
            ));
        }
    }

    /**
     * Fire the authenticated event if the dispatcher is set.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    protected function fireAuthenticatedEvent($user)
    {
        if (isset($this->events)) {
            $this->events->dispatch(new Authenticated(
                $this->name,
                $user
            ));
        }
    }

    /**
     * Fire the other device logout event if the dispatcher is set.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    protected function fireOtherDeviceLogoutEvent($user)
    {
        if (isset($this->events)) {
            $this->events->dispatch(new OtherDeviceLogout(
                $this->name,
                $user
            ));
        }
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @param array                                           $credentials
     *
     * @return void
     */
    protected function fireFailedEvent($user, array $credentials)
    {
        if (isset($this->events)) {
            $this->events->dispatch(new Failed(
                $this->name,
                $user,
                $credentials
            ));
        }
    }

    private function getTokenFromRequest()
    {
        $jwt = $this->getRequest()->bearerToken();
        if (empty($jwt)) {
            return null;
        }
        $token = (new Parser)->parse($jwt);
        if (!$this->validateToken($token)) {
            return null;
        }
        return $token;
    }

    private function validateToken(Token $token): bool
    {
        $validator = new ValidationData;
        $validator->setAudience(config('app.url'));
        $validator->setIssuer(config('app.url'));
        if (!$token->validate($validator)) {
            return false;
        }
        try {
            return $token->verify(new Sha256(), new Key($this->config['key']));
        } catch (BadMethodCallException $exception) {
            report($exception);
        }
        return false;
    }

    private function setToken(Token $token): self
    {
        $this->token = $token;
        return $this;
    }
}
