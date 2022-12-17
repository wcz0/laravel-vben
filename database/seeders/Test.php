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
        //
        // Enforcer::addPermissionForUser('admin', '', '/dashboard');
        // Enforcer::addPermissionForUser('admin', '', '/dashboard/analysis');
        // Enforcer::addPermissionForUser('admin', '', '/dashboard/workbench');

        // $data = Enforcer::getRolesForUser('1');
        // $data = Enforcer::getPermissionsForUser('roota');

        $a = [];

        $a[] += [123, 123123];
        $a[] += ['adminList', 123123];




        // $bool = Enforcer::enforce('1', '', '/dashboard/analysis');
        // dump($bool);
        dump($a);


    }
}
