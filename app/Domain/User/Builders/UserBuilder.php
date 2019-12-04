<?php

namespace Domain\User\Builders;

use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends Builder
{
    public function whereVerified(): self
    {
        $this->whereNotNull('email_verified_at');

        return $this;
    }
}
