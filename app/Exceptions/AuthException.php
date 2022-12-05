<?php

namespace App\Exceptions;

use Exception;

class AuthException extends Exception
{
    public function render($request)
    {
        return response()
            ->json([
                'code' => 400,
                'type' => 'error',
                'message' => 'Unauthenticated!',
            ], 401);
    }
}
