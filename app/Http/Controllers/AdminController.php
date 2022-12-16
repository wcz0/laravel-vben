<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Lauthz\Facades\Enforcer;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function menu()
    {
        // TODO: 判断权限
        $menus = Permission::get()->toTree();
        $this->buildMenus($menus);
        return $this->success(200, 'success', $menus);
    }

    /**
     * @param Permission $menus
     * @return void
     */
    public function buildMenus($menus)
    {
        foreach ($menus as $menu) {
            $menu->meta = [
                'title' => $menu->title,
                'icon' => $menu->icon,
                'affix' => $menu->affix == 1 ? true : false,
                'orderNo' => $menu->sort,
            ];
            unset($menu->title);
            unset($menu->icon);
            if (count($menu->children)){
                $this->buildMenus($menu->children);
            }
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->fails(400, $validator->errors());
        }
        $credentials = request(['username', 'password']);
        if (!$token = auth('admin')->setTTL(9999999999)->attempt($credentials)) {
            return $this->fails(401, 'Username or password is wrong!');
        }
        $user = auth('admin')->user();
        $roles = Enforcer::getRolesForUser($user->id);
        $array = [];
        foreach($roles as $role) {
            $array += [[
                'name' => $role,
                'value' => $role,
            ]];
        }
        $user->token = $token;
        $user->role = $array;
        return $this->success(200, 'success', $user);
    }

    public function refresh()
    {
        return $this->success(200, 'success', auth('admin')->refresh());
    }

    public function admin()
    {
        $user = auth('admin')->user();
        $roles = Enforcer::getRolesForUser($user->id);
        $array = [];
        foreach($roles as $role) {
            $array += [[
                'name' => $role,
                'value' => $role,
            ]];
        }
        $user->role = $array;
        return $this->success(200, 'success', $user);
    }
}
