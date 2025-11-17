<?php

use App\Constants\AuthConstants;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, WithFaker::class);

$loginRoute = '/api/login';

it('returns expected json on login success', function () use ($loginRoute): void {
    $password = $this->faker->password();
    $user = User::factory()->create([
        'password' => Hash::make($password),
    ]);

    $response = $this->postJson($loginRoute, [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertOk();
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('message', 'Login successful');
    $response->assertJsonPath('item.token_type', AuthConstants::TOKEN_TYPE);
    $response->assertJsonPath('item.user.id', $user->id);
    $response->assertJsonPath('item.user.name', $user->name);
    $response->assertJsonPath('item.user.email', $user->email);
    $response->assertJsonStructure([
        'success',
        'message',
        'item' => [
            'token',
            'token_type',
            'user' => ['id', 'name', 'email'],
        ],
    ]);
});

it('fails when email is incorrect', function () use ($loginRoute): void {
    $password = $this->faker->password();
    $wrongEmail = $this->faker->safeEmail();

    $response = $this->postJson($loginRoute, [
        'email' => $wrongEmail,
        'password' => $password,
    ]);

    $response->assertBadRequest();
});

it('fails when password is incorrect', function () use ($loginRoute): void {
    $correctPassword = $this->faker->password();
    $user = User::factory()->create([
        'password' => Hash::make($correctPassword),
    ]);

    $response = $this->postJson($loginRoute, [
        'email' => $user->email,
        'password' => $this->faker->password(),
    ]);

    $response->assertBadRequest();
});
