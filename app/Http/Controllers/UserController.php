<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->fails($validator->errors());
        }
        $credentials = request(['username', 'password']);
        if (!$token = auth('user')->attempt($credentials)) {
            return $this->fails(400, 'Username or password is wrong');
        }
        return $this->success('success', $token);
    }

    public function register(Request $request)
    {
        # code...
    }
}
