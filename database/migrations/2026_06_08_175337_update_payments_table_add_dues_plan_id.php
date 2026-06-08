<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
{
    // We use DB::statement to run raw SQL, which is often more reliable
    // for problematic schema changes in local environments.
    
    // 1. Drop the foreign key constraint directly if it exists
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    // 2. Safely drop the column if it exists
    if (Schema::hasColumn('payments', 'membership_fee_id')) {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['membership_fee_id']);
            $table->dropColumn('membership_fee_id');
        });
    }

    // 3. Add the new column only if it doesn't exist
    if (!Schema::hasColumn('payments', 'dues_plan_id')) {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('dues_plan_id')->nullable()->after('user_id');
            $table->foreign('dues_plan_id')->references('id')->on('dues_plans')->onDelete('set null');
        });
    }
    
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Reverse the changes
            $table->unsignedBigInteger('membership_fee_id')->nullable()->after('user_id');
            $table->dropForeign(['dues_plan_id']);
            $table->dropColumn('dues_plan_id');
        });
    }
};