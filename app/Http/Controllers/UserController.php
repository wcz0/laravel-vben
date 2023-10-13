<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->fails($validator->errors());
        }
        $user = new User();
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->save();
        return $this->success('success');
    }
}
