<?php

namespace App\Http\Controllers\Api;

use App\Data\Auth\LoginData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Services\AuthService;
use App\Traits\RespondsWithJson;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use RespondsWithJson;

    public function __construct(private AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {

        $loginDto = LoginData::from($request->validated());

        $payload = $this->authService->login(credentials: $loginDto);

        return $this->returnItemWithSuccessMessage(
            item: new LoginResource($payload),
            message: 'Login successful'
        );
    }
}
