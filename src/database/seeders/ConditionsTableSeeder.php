<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $condition_names = [
            "良好",
            "目立った傷や汚れなし",
            "やや傷や汚れあり",
            "状態が悪い",
        ];

        foreach ($condition_names as $condition_name) {
            DB::table('conditions')->insert([
                'condition_name' => $condition_name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
