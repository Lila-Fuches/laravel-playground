<?php

namespace Domain\User\Actions;

use Illuminate\Support\Str;
use Domain\User\Models\User;

final class ResetPasswordAction
{
    public function __invoke(User $user, string $passwordHash): void
    {
        $user->password = $passwordHash;

        $user->setRememberToken(Str::random(60));

        $user->save();
    }
}
