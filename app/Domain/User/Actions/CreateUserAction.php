<?php

namespace Domain\User\Actions;

use Domain\User\Models\User;

final class CreateUserAction
{
    private $sendUserVerificationAction;

    public function __construct(SendUserVerificationAction $sendUserVerificationAction)
    {
        $this->sendUserVerificationAction = $sendUserVerificationAction;
    }

    public function __invoke(array $data): User
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'verification_token' => $this->sendUserVerificationAction->generateVerificationToken($data['email'])
        ]);

        ($this->sendUserVerificationAction)->invoke($user);

        return $user;
    }
}
