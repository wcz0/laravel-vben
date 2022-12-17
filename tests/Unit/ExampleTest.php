<?php

namespace Tests\Unit;

use App\Models\Admin;
use Lauthz\Facades\Enforcer;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_true_is_true()
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
            Enforcer::addRoleForUser($user->id, 'admin');
        }
        if(empty($user)){
            $user = Admin::create([
                'id' => app('snowflake')->id(),
                'username' => 'user',
                'password' => bcrypt('root'),
                'name' => '李四',
            ]);
            Enforcer::addRoleForUser($user->id, 'admin');
        }
        Enforcer::addRoleForUser($admin->id, 'root');
        Enforcer::addRoleForUser($user->id, 'admin');
        // $this->assertTrue(true);
        Enforcer::getAllRoles();
        $per = Enforcer::getPermissionsForUser('root');
        $this->assertTrue(true);
    }
}
