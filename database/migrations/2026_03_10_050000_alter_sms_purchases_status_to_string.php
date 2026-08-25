<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change enum to string to allow flexible status values
        Schema::table('sms_purchases', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_purchases', function (Blueprint $table) {
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending')->change();
        });
    }
};
