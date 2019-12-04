<?php

namespace Tests\Unit;

use Tests\TestCase;
use Domain\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_can_create_a_user()
    {
        $user = factory(User::class)->create();
        $this->assertDatabaseHas('users', $user->toArray());
    }
}
