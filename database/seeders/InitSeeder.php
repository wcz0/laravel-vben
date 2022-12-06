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
        $this->admin();
    }

    public function menu()
    {
        $dashboard = Menu::create([
            'name' => 'Dashboard',
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
                    'path' => '/dashboard/analysis',
                    'component' => '/dashboard/analysis/index',
                    'permission' => '/dashboard/analysis',
                ],
                [
                    'name' => 'Workbench',
                    'title' => '工作台',
                    'sort' => 2,
                    'path' => '/dashboard/workbench',
                    'component' => '/dashboard/workbench/index',
                    'permission' => '/dashboard/workbench'
                ]
            ]
        ]);

        $system = Menu::create([
            'name' => 'Modify',
            'title' => '个人设置',
            'icon' => 'ant-design:setting-outlined',
            'path' => '/modify',
            'component' => 'modify',
            'sort' => 98,
            'status' => 1,
            'redirect' => '/modify/index',
            'permission' => '/modify',
            'children' => [
                [
                    'name' => 'Index',
                    'title' => '个人信息',
                    'sort' => 1,
                    'path' => '/modify/index',
                    'component' => '/modify/index/index',
                    'permission' => '/modify/index',
                ],
                [
                    'name' => 'Password',
                    'title' => '修改密码',
                    'sort' => 2,
                    'path' => '/modify/password',
                    'component' => '/modify/password/index',
                    'permission' => '/modify/password',
                ]
            ]
        ]);

        $system = Menu::create([
            'name' => 'System',
            'title' => '系统管理',
            'icon' => 'ant-design:setting-outlined',
            'path' => '/system',
            'component' => 'system',
            'sort' => 99,
            'status' => 1,
            'redirect' => '/system/menu',
            'permission' => '/system',
            'children' => [
                [
                    'name' => 'Menu',
                    'title' => '菜单管理',
                    'sort' => 1,
                    'path' => '/system/menu',
                    'component' => '/system/menu/index',
                    'permission' => '/system/menu',
                ],
                [
                    'name' => 'Permission',
                    'title' => '权限管理',
                    'sort' => 2,
                    'path' => '/system/permission',
                    'component' => '/system/permission/index',
                    'permission' => '/system/permission',
                ],
                [
                    'name' => 'Role',
                    'title' => '角色管理',
                    'sort' => 2,
                    'path' => '/system/role',
                    'component' => '/system/role/index',
                    'permission' => '/system/role',
                ],
                [
                    'name' => 'Admin',
                    'title' => '管理员管理',
                    'sort' => 3,
                    'path' => '/system/admin',
                    'component' => '/system/admin/index',
                    'permission' => '/system/admin',
                ],
                [
                    'name' => 'Log',
                    'title' => '操作日志',
                    'sort' => 4,
                    'path' => '/system/log',
                    'component' => '/system/log/index',
                    'permission' => '/system/log'
                ]
            ]
        ]);

    }

    public function admin()
    {
        $admin = Admin::where('username', 'root')->first();
        $user = Admin::where('username', 'user')->first();
        if(empty($admin)){
            Admin::create([
                'id' => app('snowflake')->id(),
                'username' => 'root',
                'password' => bcrypt('root'),
                'name' => '超级管理员',
            ]);
        }
        if(empty($user)){
            Admin::create([
                'id' => app('snowflake')->id(),
                'username' => 'user',
                'password' => bcrypt('root'),
                'name' => '李四',
            ]);
        }
    }
}
