<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->menu();


    }

    public function menu()
    {
        $dashboard = Menu::create([
            'name' => 'dashboard',
            'title' => '首页',
            'icon' => 'ant-design:appstore-outlined',
            'path' => '/dashboard',
            'component' => 'dashboard',
            'sort' => 0,
            'status' => 1,
            'redirect' => '/dashboard/analysis',
            'permission' => '/dashboard',
            'children' => [
                [
                    'name' => 'Analysis',
                    'title' => '分析页',
                    'sort' => 1,
                    'path' => 'analysis',
                    'component' => '/dashboard/analysis/index',
                    'permission' => '/dashboard/analysis'
                ],
                [
                    'name' => 'Workbench',
                    'title' => '工作台',
                    'sort' => 2,
                    'path' => 'workbench',
                    'component' => '/dashboard/workbench/index',
                    'permission' => '/dashboard/workbench'
                ]
            ]
        ]);

        $system = Menu::create([
            'name' => 'system',
            'title' => '系统管理',
            'icon' => 'ant-design:setting-outlined',
            'path' => '/system',
            'component' => 'system',
            'sort' => 1,
            'status' => 1,
            'redirect' => '/system/menu',
            'permission' => '/system',
            'children' => [
                [
                    'name' => 'Menu',
                    'title' => '菜单管理',
                    'sort' => 1,
                    'path' => 'menu',
                    'component' => '/system/menu/index',
                    'permission' => '/system/menu/index'
                ],
                [
                    'name' => 'Role',
                    'title' => '角色管理',
                    'sort' => 2,
                    'path' => 'role',
                    'component' => '/system/role/index',
                    'permission' => '/system/role'
                ],
                [
                    'name' => 'Admin',
                    'title' => '管理员管理',
                    'sort' => 3,
                    'path' => 'admin',
                    'component' => '/system/admin/index',
                    'permission' => '/system/admin'
                ],
                [
                    'name' => 'Log',
                    'title' => '操作日志',
                    'sort' => 4,
                    'path' => 'log',
                    'component' => '/system/log/index',
                    'permission' => '/system/log'
                ]
            ]
        ]);

    }

    public function admin()
    {
        // $admin = Admin::where('username''('admin')
        $admin = Admin::where('username', 'root')->first();
        if(empty($admin)){
            Admin::insert([
                'id' => app('snowflake')->id(),
                'username' => 'root',
                'password' => bcrypt('root'),
                'is_root' => 1,
            ]);
        }
    }
}
