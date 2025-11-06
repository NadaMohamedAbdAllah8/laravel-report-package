<?php

namespace Tests\Unit;

use App\Constants\AuthConstants;
use App\Data\Auth\LoginData;
use App\Data\Auth\LoginResponseData;
use App\Exceptions\ValidationException;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private AuthService $service;
    private UserService $userService;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = Mockery::mock(UserService::class);
        $this->service = new AuthService($this->userService);
    }

    public function test_login_success_returns_response_dto_and_persists_token(): void
    {
        // arrange
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

        // act
        $dto = LoginData::from([
            'email' => $email,
            'password' => $password,
        ]);

        $response = $this->service->login($dto);

        // assert
        $this->assertInstanceOf(LoginResponseData::class, $response);
        $this->assertIsString($response->token);
        $this->assertSame(AuthConstants::TOKEN_LENGTH, strlen($response->token));
        $this->assertSame(AuthConstants::TOKEN_TYPE, $response->token_type);
        $this->assertInstanceOf(User::class, $response->user);
        $this->assertSame($user->id, $response->user->id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'api_token' => $response->token,
        ]);
    }

    public function test_login_throws_when_user_not_found(): void
    {
        // arrange
        $missingEmail = $this->faker->safeEmail();
        $this->userService->shouldReceive('getOneByEmail')
            ->once()
            ->with($missingEmail)
            ->andReturn(null);

        $dto = LoginData::from([
            'email' => $missingEmail,
            'password' => $this->faker->password(),
        ]);

        // assert
        $this->expectException(ValidationException::class);

        // act
        $this->service->login(credentials: $dto);
    }

    public function test_login_throws_when_password_invalid(): void
    {
        // arrange
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

        // assert
        $this->expectException(ValidationException::class);

        // act
        $this->service->login(credentials: $dto);
    }

    public function test_login_throws_when_email_invalid(): void
    {
        // arrange
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

        // assert
        $this->expectException(ValidationException::class);

        // act
        $this->service->login(credentials: $dto);
    }
}
