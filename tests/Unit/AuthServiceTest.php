<?php

use App\Constants\AuthConstants;
use App\Data\Auth\LoginData;
use App\Data\Auth\LoginResponseData;
use App\Exceptions\ValidationException;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->userService = Mockery::mock(UserService::class);
    $this->service = new AuthService($this->userService);
});

test('login success returns response dto and persists token', function (): void {
    $email = $this->faker->safeEmail();
    $password = $this->faker->password();
    $user = User::factory()->create([
        'email' => $email,
        'password' => Hash::make($password),
    ]);

    $this->userService->shouldReceive('getOneByEmail')
        ->once()
        ->with($email)
        ->andReturn($user);

    $dto = LoginData::from([
        'email' => $email,
        'password' => $password,
    ]);

    $response = $this->service->login($dto);

    expect($response)->toBeInstanceOf(LoginResponseData::class);
    expect($response->token)->toBeString();
    expect(strlen($response->token))->toBe(AuthConstants::TOKEN_LENGTH);
    expect($response->token_type)->toBe(AuthConstants::TOKEN_TYPE);
    expect($response->user)->toBeInstanceOf(User::class);
    expect($response->user->id)->toBe($user->id);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'api_token' => $response->token,
    ]);
});

test('login throws when user not found', function (): void {
    $missingEmail = $this->faker->safeEmail();
    $this->userService->shouldReceive('getOneByEmail')
        ->once()
        ->with($missingEmail)
        ->andReturn(null);

    $dto = LoginData::from([
        'email' => $missingEmail,
        'password' => $this->faker->password(),
    ]);

    $this->expectException(ValidationException::class);

    $this->service->login(credentials: $dto);
});

test('login throws when password invalid', function (): void {
    $email = $this->faker->safeEmail();
    $correctPassword = $this->faker->password();
    $user = new User([
        'email' => $email,
        'password' => Hash::make($correctPassword),
    ]);

    $this->userService->shouldReceive('getOneByEmail')
        ->once()
        ->with($email)
        ->andReturn($user);

    $dto = LoginData::from([
        'email' => $email,
        'password' => $this->faker->password(),
    ]);

    $this->expectException(ValidationException::class);

    $this->service->login(credentials: $dto);
});

test('login throws when email invalid', function (): void {
    $existingEmail = $this->faker->safeEmail();
    $wrongEmail = $this->faker->safeEmail();
    $password = $this->faker->password();

    User::factory()->create([
        'email' => $existingEmail,
        'password' => Hash::make($password),
    ]);

    $this->userService->shouldReceive('getOneByEmail')
        ->once()
        ->with($wrongEmail)
        ->andReturn(null);

    $dto = LoginData::from([
        'email' => $wrongEmail,
        'password' => $password,
    ]);

    $this->expectException(ValidationException::class);

    $this->service->login(credentials: $dto);
});
