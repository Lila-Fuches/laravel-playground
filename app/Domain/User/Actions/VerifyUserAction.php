<?php

namespace Domain\User\Actions;

use Domain\User\Models\User;

final class VerifyUserAction
{
    public function __invoke(User $user): void
    {
        $user->email_verified_at = now();

        $user->save();
    }
}
