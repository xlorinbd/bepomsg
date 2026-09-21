<?php

    namespace Database\Seeders;


    use Illuminate\Database\Seeder;
    use App\Models\Currency;

    class CurrenciesSeeder extends Seeder
    {
        /**
         * Run the database seeders.
         *
         * @return void
         */
        public function run()
        {
            $currency_data = [
                [
                    'uid'     => uniqid(),
                    'user_id' => 1,
                    'name'    => 'Bangladeshi Taka',
                    'code'    => 'BDT',
                    'format'  => '৳{PRICE}',
                    'status'  => true,
                ],
            ];

            foreach ($currency_data as $data) {
                Currency::create($data);
            }

        }

    }
