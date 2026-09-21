<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class Countries extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $c = new Country();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $c->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $countries = [];
        $countries[] = ['iso_code' => 'BD', 'name' => 'Bangladesh', 'country_code' => '880', 'status' => true];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
