<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'user_postal_code' => '140-0000',
            'user_address' => '東京都品川区',
            'user_building' => '大井ビル',
            'created_at' => now(),
            'updated_at' => now(),
            'first_login' => false,
        ];
        DB::table('users')->insert($param);
    }
}
