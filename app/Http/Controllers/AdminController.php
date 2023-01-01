<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Lauthz\Facades\Enforcer;
use Illuminate\Http\Request;
use stdClass;

class AdminController extends Controller
{
    public function menu(Request $request)
    {
        $admin = $request->user('admin');
        $roles = Enforcer::getRolesForUser($admin->id);
        $permissions = [];
        foreach ($roles as $role)
        {
            $permissions[] = array_column(Enforcer::getPermissionsForUser($role), 2);
        }
        $permissions = array_unique(array_merge(...$permissions));
        $menus = Permission::where('type', 0)
            ->where('status', 1)
            ->whereIn('permission', $permissions)
            ->get([
                'id',
                'path',
                'name',
                'redirect',
                'component',
                'parent_id',
                'title',
                'affix',
                'icon',
                'sort',
                '_lft',
                '_rgt',
                'permission',
            ])
            ->toTree();
        $this->treeFormat($menus);
        return $this->success('success', $menus);
    }

    /**
     * @param Permission $menus
     * @return void
     */
    public function treeFormat($menus)
    {
        foreach ($menus as $menu)
        {
            $menu->meta = [
                'title' => $menu->title,
                'icon' => $menu->icon,
                'affix' => $menu->affix == 1 ? true : false,
                'sort' => $menu->sort,
            ];
            unset($menu->parent_id);
            unset($menu->_lft);
            unset($menu->_rgt);
            unset($menu->title);
            unset($menu->icon);
            unset($menu->affix);
            if (count($menu->children))
            {
                $this->treeFormat($menu->children);
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
            return $this->fails($validator->errors());
        }
        $credentials = request(['username', 'password']);
        if (!$token = auth('admin')->setTTL(9999999999)->attempt($credentials))
        {
            return $this->fails('Username or password is wrong!');
        }
        $user = auth('admin')->user();
        $roles = Enforcer::getRolesForUser($user->id);
        $roles = Role::where('status', 1)
            ->whereIn('value', $roles)
            ->get(['name', 'value',]);
        $result = new stdClass();
        $result->id = $user->id;
        $result->username = $user->username;
        $result->name = $user->name;
        $result->avatar = $user->avatar;
        $result->email = $user->email;
        $result->phone = $user->phone;
        $result->token = $token;
        $result->role = $roles->toArray();
        return $this->success('success', $result);
    }

    public function refresh()
    {
        return $this->success('success', auth('admin')->refresh());
    }

    public function admin(Request $request)
    {
        $user = $request->user();
        $roles = Enforcer::getRolesForUser($user->id);
        $roles = Role::where('status', 1)
            ->whereIn('value', $roles)
            ->get(['name', 'value',]);
        $user->role = $roles->toArray();
        return $this->success('success', $user);
    }
}