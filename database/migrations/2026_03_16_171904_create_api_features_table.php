<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Seed initial data
        DB::table('api_features')->insert([
            ['name' => 'Contacts API', 'slug' => 'api_contacts', 'status' => 1],
            ['name' => 'Contact Groups API', 'slug' => 'api_contact_groups', 'status' => 1],
            ['name' => 'SMS API', 'slug' => 'api_sms', 'status' => 1],
            ['name' => 'Voice API', 'slug' => 'api_voice', 'status' => 1],
            ['name' => 'MMS API', 'slug' => 'api_mms', 'status' => 1],
            ['name' => 'WhatsApp API', 'slug' => 'api_whatsapp', 'status' => 1],
            ['name' => 'Viber API', 'slug' => 'api_viber', 'status' => 1],
            ['name' => 'OTP API', 'slug' => 'api_otp', 'status' => 1],
            ['name' => 'Profile API', 'slug' => 'api_profile', 'status' => 1],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_features');
    }
};
