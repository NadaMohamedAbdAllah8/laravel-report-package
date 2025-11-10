<?php

namespace App\Exceptions;

use App\Traits\RespondsWithJson;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ValidationException extends Exception
{
    use RespondsWithJson;

    public function render(): JsonResponse
    {
        return $this->returnErrorMessage($this->getMessage(), Response::HTTP_BAD_REQUEST);
    }
}
