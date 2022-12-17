<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Lauthz\Facades\Enforcer;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function menu(Request $request)
    {
        $admin = $request->user('admin');
        $roles = Enforcer::getRolesForUser($admin->id);
        $permissions = [];
        foreach ($roles as $role) {
            $permissions[] = array_column(Enforcer::getPermissionsForUser($role), 2);
        }
        $permissions = array_unique(array_merge(...$permissions));
        $menus = Permission::whereIn('permission', $permissions)
            ->get()
            ->toTree();
        $this->buildMenus($menus);
        return $this->success(200, 'success', $menus);
    }

    /**
     * @param Permission $menus
     * @return void
     */
    public function buildMenus($menus)
    {
        foreach ($menus as $menu)
        {
            $menu->meta = [
                'title' => $menu->title,
                'icon' => $menu->icon,
                'affix' => $menu->affix == 1 ? true : false,
                'orderNo' => $menu->sort,
            ];
            unset($menu->title);
            unset($menu->icon);
            if (count($menu->children))
            {
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
        if ($validator->fails())
        {
            return $this->fails(400, $validator->errors());
        }
        $credentials = request(['username', 'password']);
        if (!$token = auth('admin')->setTTL(9999999999)->attempt($credentials))
        {
            return $this->fails(401, 'Username or password is wrong!');
        }
        $user = auth('admin')->user();
        $roles = Enforcer::getRolesForUser($user->id);
        $roles = Role::where('status', 1)
            ->whereIn('value', $roles)
            ->get(['name', 'value',]);
        $user->token = $token;
        $user->role = $roles->toArray();
        return $this->success(200, 'success', $user);
    }

    public function refresh()
    {
        return $this->success(200, 'success', auth('admin')->refresh());
    }

    public function admin(Request $request)
    {
        $user = $request->user();
        $roles = Enforcer::getRolesForUser($user->id);
        $roles = Role::where('status', 1)
            ->whereIn('value', $roles)
            ->get(['name', 'value',]);
        $user->role = $roles->toArray();
        return $this->success(200, 'success', $user);
    }
}