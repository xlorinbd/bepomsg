<?php

use App\Models\PaymentMethods;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Adds the PayStation gateway as a selectable payment method without
     * touching (or truncating) any existing gateway rows/config.
     *
     * @return void
     */
    public function up()
    {
        PaymentMethods::firstOrCreate(
            ['type' => PaymentMethods::TYPE_PAYSTATION],
            [
                'name'    => 'PayStation',
                'options' => json_encode([
                    'merchantId'  => 'Merchant ID',
                    'password'    => 'Password',
                    'environment' => 'sandbox',
                ]),
                'status'  => false,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        PaymentMethods::where('type', PaymentMethods::TYPE_PAYSTATION)->delete();
    }
};
