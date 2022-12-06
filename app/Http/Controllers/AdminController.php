<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Lauthz\Facades\Enforcer;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function menu()
    {
        // TODO: 判断权限
        $menus = Menu::get()->toTree();
        $this->buildMenus($menus);
        return $this->success(200, 'success', $menus);
    }

    public function buildMenus($menus)
    {
        foreach ($menus as $menu) {
            $menu->meta = [
                'title' => $menu->title,
                'icon' => $menu->icon,
            ];
            unset($menu->title);
            unset($menu->icon);
            if (count($menu->children)){
                $this->buildMenus($menu->children);
            }
        }
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
