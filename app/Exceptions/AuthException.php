<?php

namespace App\Exceptions;

use Exception;

class AuthException extends Exception
{
    public function render($request)
    {
        return response()
            ->json([
                'code' => 401,
                'message' => 'Unauthenticated!',
            ], 401);
    }
}
