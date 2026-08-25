<?php

    namespace Database\Factories;

    use App\Models\Blacklists;
    use Illuminate\Database\Eloquent\Factories\Factory;
    use Faker\Factory as Faker;

    /**
     * @extends Factory<Blacklists>
     */
    class BlacklistsFactory extends Factory
    {

        protected $model = Blacklists::class;

        /**
         * Define the model's default state.
         *
         * @return array<string, mixed>
         */
        public function definition(): array
        {

            $faker = Faker::create();
            return [
                'user_id' => 3,
                'number'  => ltrim($faker->unique()->e164PhoneNumber(), '+'), // Removes leading '+' directly
                'reason'  => $faker->optional(0.9)->randomElement(['Optout by user', 'Blacklisted by admin', 'Other reasons']),
            ];
        }

    }
