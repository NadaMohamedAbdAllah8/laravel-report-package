<?php

use App\Models\User;
use App\Services\UserService;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->service = new UserService;
});

test('getOneByEmail returns user when user exists', function (): void {
    $email = $this->faker->safeEmail();
    $fakedUser = User::factory()->create(['email' => $email]);

    $user = $this->service->getOneByEmail($email);

    expect($user)->not->toBeNull();
    expect($user)->toBeInstanceOf(User::class);
    expect($user?->id)->toBe($fakedUser->id);
    expect($user?->email)->toBe($email);
});

test('getOneByEmail returns null when user does not exist', function (): void {
    $userEmail = $this->faker->safeEmail();
    $randomEmail = $this->faker->safeEmail();

    User::factory()->create(['email' => $userEmail]);

    $user = $this->service->getOneByEmail($randomEmail);

    expect($user)->toBeNull();
});
