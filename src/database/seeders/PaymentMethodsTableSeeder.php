<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payment_methods = [
            "コンビニ払い",
            "カード支払い",
        ];

        foreach ($payment_methods as $payment_method) {
            DB::table('payment_methods')->insert([
                'payment_method' => $payment_method,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
