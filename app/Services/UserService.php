<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getOneByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
