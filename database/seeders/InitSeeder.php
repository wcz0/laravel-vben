<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Lauthz\Facades\Enforcer;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->role();
        $this->permission();
        $this->admin();
    }

    public function permission()
    {
        $dashboard = Permission::create([
            'name' => 'Dashboard',
            'title' => '首页',
            'icon' => 'ant-design:appstore-outlined',
            'path' => '/dashboard',
            'component' => 'LAYOUT',
            'sort' => 0,
            'status' => 1,
            'redirect' => '/dashboard/analysis',
            'permission' => '/dashboard',
            'type' => 0,
            'children' => [
                [
                    'name' => 'Analysis',
                    'title' => '分析页',
                    'sort' => 1,
                    'path' => '/dashboard/analysis',
                    'component' => '/dashboard/analysis/index',
                    'permission' => '/dashboard/analysis',
                    'type' => 0,
                ],
                [
                    'name' => 'Workbench',
                    'title' => '工作台',
                    'sort' => 2,
                    'path' => '/dashboard/workbench',
                    'component' => '/dashboard/workbench/index',
                    'permission' => '/dashboard/workbench',
                    'type' => 0,
                ]
            ]
        ]);

        $system_modify = Permission::create([
            'name' => 'Modify',
            'title' => '个人设置',
            'icon' => 'ant-design:setting-outlined',
            'path' => '/modify',
            'component' => 'LAYOUT',
            'sort' => 98,
            'status' => 1,
            'redirect' => '/modify/index',
            'permission' => '/modify',
            'type' => 0,
            'children' => [
                [
                    'name' => 'Index',
                    'title' => '个人信息',
                    'sort' => 1,
                    'path' => '/modify/index',
                    'component' => '/modify/index/index',
                    'permission' => '/modify/index',
                    'type' => 0,
                ],
                [
                    'name' => 'Password',
                    'title' => '修改密码',
                    'sort' => 2,
                    'path' => '/modify/password',
                    'component' => '/modify/password/index',
                    'permission' => '/modify/password',
                    'type' => 0,
                ]
            ]
        ]);

        $system_menu = Permission::create([
            'name' => 'System',
            'title' => '系统管理',
            'icon' => 'ant-design:setting-outlined',
            'path' => '/system',
            'component' => 'LAYOUT',
            'sort' => 99,
            'status' => 1,
            'redirect' => '/system/permission',
            'permission' => '/system',
            'type' => 0,
            'children' => [
                [
                    'name' => 'Permission',
                    'title' => '权限管理',
                    'sort' => 2,
                    'path' => '/system/permission',
                    'component' => '/system/permission/index',
                    'permission' => '/system/permission',
                    'type' => 0,
                    'children' => [
                        [
                            'name' => 'Create',
                            'title' => '添加权限',
                            'sort' => 1,
                            'permission' => '/system/permission/create',
                            'type' => 1,
                        ],
                        [
                            'name' => 'Update',
                            'title' => '修改权限',
                            'sort' => 2,
                            'permission' => '/system/permission/update',
                            'type' => 1,
                        ],
                        [
                            'name' => 'Delete',
                            'title' => '删除权限',
                            'permission' => '/system/permission/delete',
                            'type' => 1,
                        ],
                    ]
                ],
                [
                    'name' => 'Role',
                    'title' => '角色管理',
                    'sort' => 2,
                    'path' => '/system/role',
                    'component' => '/system/role/index',
                    'permission' => '/system/role',
                    'type' => 0,
                    'children' => [
                        [
                            'name' => 'Create',
                            'title' => '添加角色',
                            'sort' => 1,
                            'permission' => '/system/role/create',
                            'type' => 1,
                        ],
                        [
                            'name' => 'Update',
                            'title' => '修改角色',
                            'sort' => 2,
                            'permission' => '/system/role/update',
                            'type' => 1,
                        ],
                        [
                            'name' => 'Delete',
                            'title' => '删除角色',
                            'permission' => '/system/role/delete',
                            'type' => 1,
                        ],
                    ]
                ],
                [
                    'name' => 'Admin',
                    'title' => '管理员管理',
                    'sort' => 3,
                    'path' => '/system/admin',
                    'component' => '/system/admin/index',
                    'permission' => '/system/admin',
                    'type' => 0,
                    'children' => [
                        [
                            'name' => 'Create',
                            'title' => '添加管理员',
                            'sort' => 1,
                            'permission' => '/system/admin/create',
                            'type' => 1,
                        ],
                        [
                            'name' => 'Update',
                            'title' => '修改管理员',
                            'sort' => 2,
                            'permission' => '/system/admin/update',
                            'type' => 1,
                        ],
                        [
                            'name' => 'Delete',
                            'title' => '删除管理员',
                            'permission' => '/system/admin/delete',
                            'type' => 1,
                        ],
                    ]
                ],
                [
                    'name' => 'Log',
                    'title' => '操作日志',
                    'sort' => 4,
                    'path' => '/system/log',
                    'component' => '/system/log/index',
                    'permission' => '/system/log',
                    'type' => 0,
                ]
            ]
        ]);

    }

    public function role()
    {
        $root = Role::insert([
            [
                'value' => 'root',
                'name' => '超级管理员',
                'status' => 1,
                'desc' => 'root role , have all permission',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value' => 'admin',
                'name' => '管理员',
                'status' => 1,
                'desc' => 'admin role , have some permission',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Enforcer::addPermissionForUser('root', '*', '*');

        Enforcer::addPermissionForUser('admin', '/dashboard', 'get');
        Enforcer::addPermissionForUser('admin', '/dashboard/workbench', 'get');
        Enforcer::addPermissionForUser('admin', '/dashboard/analysis', 'get');
        Enforcer::addPermissionForUser('admin', '/modify', 'get');
        Enforcer::addPermissionForUser('admin', '/modify/index', 'get');
        Enforcer::addPermissionForUser('admin', '/modify/password', 'post');
    }

    public function admin()
    {
        $admin = Admin::create([
            'id' => 1,
            'username' => 'root',
            'password' => bcrypt('root'),
            'name' => '超级管理员',
            'phone' => '138888888888',
            'email' => 'qq@qq.com',
            'gender' => 1,
            'birthday' => fake()->date(),
        ]);
        $user = Admin::create([
            'id' => app('snowflake')->id(),
            'username' => 'user',
            'password' => bcrypt('root'),
            'name' => '李四',
            'gender' => 2,
            'phone' => '138888888888',
            'email' => '132@qq.com',
            'birthday' => fake()->date(),
        ]);
        Enforcer::addRoleForUser($admin->id, 'root');
        Enforcer::addRoleForUser($user->id, 'admin');
    }
}