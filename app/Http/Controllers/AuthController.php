<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function adminLogin()
    {
        $credentials = request(['username', 'password']);

        if (! $token = auth('admin')->attempt($credentials)) {
            return $this->fails(401, 'Unauthorized');
        }
        return $this->success(200, 'success', $token);
    }

    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->fails(401, $validator->errors());
        }
        $credentials = request(['username', 'password']);
        if (! $token = auth('user')->attempt($credentials)) {
            return $this->fails(401, 'Unauthorized');
        }
        return $this->success(200, 'success', $token);
    }

    public function register(Request $request)
    {
        # code...
    }

}
