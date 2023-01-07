<?php

namespace App\Http\Controllers;

use Carbon\Traits\Mixin;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function fails(string $message, int $code = 400)
    {
        return response()->json([
            'code' =>$code,
            'type' => 'error',
            'message' => $message,
        ]);
    }

    public function success(string $message, mixed $data = [], int $code = 200)
    {
        return response()->json([
            'code' => $code,
            'type' => 'success',
            'message' => $message,
            'result' => $data,
        ]);
    }

}
