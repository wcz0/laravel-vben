<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Lauthz\Facades\Enforcer;

class Test extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::where('username', 'root')->first();
        $user = Admin::where('username', 'user')->first();
        if(empty($admin)){
            $admin = Admin::create([
                'id' => app('snowflake')->id(),
                'username' => 'root',
                'password' => bcrypt('root'),
                'name' => '超级管理员',
            ]);
        }
        if(empty($user)){
            $user = Admin::create([
                'id' => app('snowflake')->id(),
                'username' => 'user',
                'password' => bcrypt('root'),
                'name' => '李四',
            ]);
        }
        Enforcer::addRoleForUser($admin->id, 'root');
        Enforcer::addRoleForUser($user->id, 'admin');
    }
}
