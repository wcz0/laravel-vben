<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login()
    {
        $credentials = request(['name', 'password']);

        if (! $token = auth('admin')->attempt($credentials)) {
            return $this->fails(401, 'Unauthorized');
        }

        return $this->success(200, 'success', $this->respondWithToken($token));
    }
}
