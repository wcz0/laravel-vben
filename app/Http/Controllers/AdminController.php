<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function menu()
    {
        // TODO: 判断权限
        $menus = Menu::all();
        return $this->success(200, 'success', $menus);
    }

    public function admin()
    {
        return $this->success(200, 'success', auth('admin')->user());
    }
}
