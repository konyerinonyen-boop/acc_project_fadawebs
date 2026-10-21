<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('payments', 'description')) {
            Schema::table('payments', function (Blueprint $table): void {
                $table->string('description')->nullable()->after('payment_method');
            });
        }

        $membershipTypeIds = DB::table('payment_types')
            ->whereIn(DB::raw('LOWER(name)'), ['dues', 'membership', 'membership_payment', 'membership fee'])
            ->pluck('id');

        if ($membershipTypeIds->isNotEmpty()) {
            DB::table('payments')
                ->whereNull('description')
                ->whereIn('payment_type_id', $membershipTypeIds)
                ->update(['description' => 'membership_fee']);
        }

        $donationTypeIds = DB::table('payment_types')
            ->whereIn(DB::raw('LOWER(name)'), ['donation', 'donations'])
            ->pluck('id');

        if ($donationTypeIds->isNotEmpty()) {
            DB::table('payments')
                ->whereNull('description')
                ->whereIn('payment_type_id', $donationTypeIds)
                ->update(['description' => 'donation']);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'description')) {
            Schema::table('payments', function (Blueprint $table): void {
                $table->dropColumn('description');
            });
        }
    }
};
