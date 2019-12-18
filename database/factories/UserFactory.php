<?php

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Domain\User\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

$factory->define(User::class, function (Faker $faker) {
    return [
        'uuid' => $faker->uuid,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $email = $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => Hash::make('password'), // password
        'verification_token' => sha1(Hash::make($email . (string) Uuid::uuid4())),
        'remember_token' => Str::random(10),
    ];
});
