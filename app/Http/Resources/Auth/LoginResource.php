<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\User\LoggedInUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    public function toArray($request): array
    {

        return [
            'token' => $this->token,
            'token_type' => $this->token_type,
            'user' => new LoggedInUserResource($this->user),
        ];
    }
}
