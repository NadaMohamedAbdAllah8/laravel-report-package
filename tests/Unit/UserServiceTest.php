<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService;
    }

    public function test_get_one_by_email_returns_user_when_user(): void
    {
        // arrange
        $email = $this->faker->safeEmail();
        $fakedUser = User::factory()->create(['email' => $email]);

        // act
        $user = $this->service->getOneByEmail($email);

        // assert
        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($fakedUser->id, $user->id);
        $this->assertSame($email, $user->email);
    }

    public function test_get_one_by_email_returns_null_when_not_user(): void
    {
        // arrange
        $userEmail = $this->faker->safeEmail();
        $randomEmail = $this->faker->safeEmail();

        $fakedUser = User::factory()->create(['email' => $userEmail]);

        // act
        $user = $this->service->getOneByEmail($randomEmail);

        // assert
        $this->assertNull($user);
    }
}
