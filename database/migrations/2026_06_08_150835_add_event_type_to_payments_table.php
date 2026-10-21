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
        if (! Schema::hasColumn('payments', 'event_type')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('event_type')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('payments', 'event_type')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('event_type');
            });
        }
    }
};
