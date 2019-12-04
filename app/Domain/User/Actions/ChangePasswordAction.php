<?php

namespace Domain\User\Actions;

use Domain\User\Models\User;

final class ChangePasswordAction
{
    public function __invoke(User $ser, string $newPassword): void
    {
        $user->password = $newPassword;
        $user->save();
    }
}
