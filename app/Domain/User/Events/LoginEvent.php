<?php

namespace Domain\User\Events;

use Spatie\EventSourcing\ShouldBeStored;

class LoginEvent implements ShouldBeStored
{
    /** @var string */
    public string $uuid;

    /**
     * @var array
     */
    public array $userAttributes;

    public function __construct(string $uuid, array $userAttributes)
    {
        $this->uuid = $uuid;
        $this->userAttributes = $userAttributes;
    }
}
