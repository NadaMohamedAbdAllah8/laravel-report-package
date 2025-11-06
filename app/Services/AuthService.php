<?php

namespace App\Services;

use App\Constants\AuthConstants;
use App\Data\Auth\LoginData;
use App\Data\Auth\LoginResponseData;
use App\Exceptions\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(private UserService $userService) {}

    public function login(LoginData $credentials): LoginResponseData
    {
        $user = $this->userService->getOneByEmail(email: $credentials->email);

        if (! $user || ! Hash::check($credentials->password, $user->password)) {
            throw new ValidationException('The provided credentials are incorrect.');
        }

        $token = $this->getToken();
        $this->setUserToken(user: $user, token: $token);

        return new LoginResponseData(
            token: $token,
            token_type: AuthConstants::TOKEN_TYPE,
            user: $user
        );
    }

    private function getToken(): string
    {
        return Str::random(AuthConstants::TOKEN_LENGTH);
    }

    private function setUserToken(User $user, string $token): void
    {
        $user->api_token = $token;
        $user->save();
    }
}
