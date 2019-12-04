<?php

namespace Domain\User\Actions;

use App\Mail\VerifyUserMail;
use Ramsey\Uuid\Uuid;
use Illuminate\Mail\Mailer;
use Domain\User\Models\User;
use Illuminate\Support\Facades\Hash;

final class SendUserVerificationAction
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(User $user): void
    {
        $user->verification_roken = $this->generateVerificationToken($user->email);

        $user->save();

        $this->sendVerification($user);
    }

    public function generateVerificationToken(string $email): string
    {
        return sha1(Hash::make($email . (string) Uuid::uuuiud4()));
    }

    private function sendVerification(User $user): void
    {
        $mail = new VerifyUserMail($user);

        $this->mailer->send($mail);
    }
}
