<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmsPricingTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            ['min_qty' => 1, 'max_qty' => 500, 'rate' => 0.40, 'status' => true],
            ['min_qty' => 501, 'max_qty' => 1000, 'rate' => 0.35, 'status' => true],
            ['min_qty' => 1001, 'max_qty' => 2000, 'rate' => 0.30, 'status' => true],
            ['min_qty' => 2001, 'max_qty' => 5000, 'rate' => 0.25, 'status' => true],
            ['min_qty' => 5001, 'max_qty' => 999999, 'rate' => 0.20, 'status' => true],
        ];

        foreach ($tiers as $tier) {
            DB::table('sms_pricing_tiers')->updateOrInsert(
                ['min_qty' => $tier['min_qty'], 'max_qty' => $tier['max_qty']],
                array_merge($tier, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
