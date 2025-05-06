<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'item_name' => '腕時計',
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'item_image' => 'Armani+Mens+Clock.jpg',
            'condition_id' => '1',
            'user_id' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_name' => 'HDD',
            'price' => 5000,
            'description' => '高速で信頼性の高いハードディスク',
            'item_image' => 'HDD+Hard+Disk.jpg',
            'condition_id' => '2',
            'user_id' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_name' => '玉ねぎ3束',
            'price' => 300,
            'description' => '新鮮な玉ねぎ3束のセット',
            'item_image' => 'iLoveIMG+d.jpg',
            'condition_id' => '3',
            'user_id' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_name' => '革靴',
            'price' => 4000,
            'description' => 'クラシックなデザインの革靴',
            'item_image' => 'Leather+Shoes+Product+Photo.jpg',
            'condition_id' => '4',
            'user_id' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);
        $param = [
            'item_name' => 'ノートPC',
            'price' => 45000,
            'description' => '高性能なノートパソコン',
            'item_image' => 'Living+Room+Laptop.jpg',
            'condition_id' => '1',
            'user_id' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_name' => 'マイク',
            'price' => 8000,
            'description' => '高音質のレコーディング用マイク',
            'item_image' => 'Music+Mic+4632231.jpg',
            'condition_id' => '2',
            'user_id' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_name' => 'ショルダーバッグ',
            'price' => 3500,
            'description' => 'おしゃれなショルダーバッグ',
            'item_image' => 'Purse+fashion+pocket.jpg',
            'condition_id' => '3',
            'user_id' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_name' => 'タンブラー',
            'price' => 500,
            'description' => '使いやすいタンブラー',
            'item_image' => 'Tumbler+souvenir.jpg',
            'condition_id' => '4',
            'user_id' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_name' => 'コーヒーミル',
            'price' => 4000,
            'description' => '手動のコーヒーミル',
            'item_image' => 'Waitress+with+Coffee+Grinder.jpg',
            'condition_id' => '1',
            'user_id' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);

        $param = [
            'item_name' => 'メイクセット',
            'price' => 2500,
            'description' => '便利なメイクアップセット',
            'item_image' => 'make+set.jpg',
            'condition_id' => '2',
            'user_id' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('items')->insert($param);
    }
}