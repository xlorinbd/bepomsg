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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable();
            $table->string('gender')->nullable();
            $table->string('nid_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('company_name')->nullable();
            $table->text('company_address')->nullable();
            $table->text('purpose_of_use')->nullable();
            $table->string('nid_upload')->nullable();
            $table->string('trade_license')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'gender', 'nid_number', 'date_of_birth', 
                'company_name', 'company_address', 'purpose_of_use', 
                'nid_upload', 'trade_license'
            ]);
        });
    }
};
