<?php

namespace Tests\Feature;

use App\Constants\AuthConstants;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker;

    private string $route = '/api/login';

    public function test_login_success_returns_expected_json(): void
    {
        // arrange
        $password = $this->faker->password();
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        // act
        $response = $this->postJson($this->route, [
            'email' => $user->email,
            'password' => $password,
        ]);

        // assert
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
    }

    public function test_login_fails_with_wrong_email(): void
    {
        // arrange
        $password = $this->faker->password();
        // no user with this email
        $wrongEmail = $this->faker->safeEmail();

        // act
        $response = $this->postJson($this->route, [
            'email' => $wrongEmail,
            'password' => $password,
        ]);

        // assert
        $response->assertBadRequest();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        // arrange
        $correctPassword = $this->faker->password();
        $user = User::factory()->create([
            'password' => Hash::make($correctPassword),
        ]);

        // act
        $response = $this->postJson($this->route, [
            'email' => $user->email,
            'password' => $this->faker->password(),
        ]);

        // assert
        $response->assertBadRequest();
    }
}
