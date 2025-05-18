<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category_names = [
            "ファッション",
            "家電",
            "インテリア",
            "レディース",
            "メンズ",
            "コスメ",
            "本",
            "ゲーム",
            "スポーツ",
            "キッチン",
            "ハンドメイド",
            "アクセサリー",
            "おもちゃ",
            "ベビー・キッズ",
        ];

        foreach ($category_names as $category_name) {
            DB::table('categories')->insert([
                'category_name' => $category_name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
