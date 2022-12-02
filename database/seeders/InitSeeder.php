<?php

namespace Database\Seeders;

use App\Models\Admin;
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
        $admin = [
            'id' => app('snowflake')->id(),
            'username' => 'root',
            'password' => bcrypt('root'),
            'is_root' => 1,
        ];

        Admin::insert($admin);


    }
}
