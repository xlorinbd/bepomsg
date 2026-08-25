<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            // Field requirements in registration form (1=required, 0=optional, -1=hidden)
            ['setting' => 'req_username', 'value' => '1'],
            ['setting' => 'req_first_name', 'value' => '1'],
            ['setting' => 'req_gender', 'value' => '1'],
            ['setting' => 'req_email', 'value' => '1'],
            ['setting' => 'req_password', 'value' => '1'],
            ['setting' => 'req_phone', 'value' => '1'],
            ['setting' => 'req_nid_number', 'value' => '1'],
            ['setting' => 'req_date_of_birth', 'value' => '1'],
            ['setting' => 'req_address', 'value' => '1'],
            ['setting' => 'req_company_name', 'value' => '0'],
            ['setting' => 'req_company_address', 'value' => '0'],
            ['setting' => 'req_purpose_of_use', 'value' => '1'],
            ['setting' => 'req_nid_upload', 'value' => '1'],
            ['setting' => 'req_trade_license', 'value' => '0'],
            ['setting' => 'req_profile_photo', 'value' => '0'],
        ];

        foreach ($settings as $setting) {
            \App\Models\AppConfig::updateOrCreate(
                ['setting' => $setting['setting']],
                ['value' => $setting['value']]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $settings = [
            'req_username',
            'req_first_name',
            'req_gender',
            'req_email',
            'req_password',
            'req_phone',
            'req_nid_number',
            'req_date_of_birth',
            'req_address',
            'req_company_name',
            'req_company_address',
            'req_purpose_of_use',
            'req_nid_upload',
            'req_trade_license',
            'req_profile_photo',
        ];

        \App\Models\AppConfig::whereIn('setting', $settings)->delete();
    }
};
