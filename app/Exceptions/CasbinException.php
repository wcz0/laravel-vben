<?php

namespace App\Exceptions;

use Exception;

class CasbinException extends Exception
{
    public function render($request)
    {
        return response()
            ->json([
                'code' => 403,
                'type' => 'error',
                'message' => 'You no have Permission!',
            ], 401);
    }
}
