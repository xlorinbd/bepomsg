<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only add column if it doesn't exist
        if (!Schema::hasColumn('users', 'sms_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('sms_balance')->default(0)->after('sms_unit');
            });
        }

        // Migrate existing sms_unit values to sms_balance
        // sms_unit = -1 means unlimited (legacy), treat as 0 for new system
        // sms_unit = NULL treat as 0
        // sms_unit > 0 copy the value to sms_balance
        $prefix = DB::getTablePrefix();
        DB::statement("UPDATE {$prefix}users SET sms_balance = CASE 
            WHEN sms_unit IS NULL THEN 0 
            WHEN CAST(sms_unit AS SIGNED) <= 0 THEN 0 
            ELSE CAST(sms_unit AS SIGNED) 
        END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sms_balance');
        });
    }
};
