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
                'message' => 'You do not have permission!',
            ], 401);
    }

}
