<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments') && ! Schema::hasColumn('payments', 'description')) {
            Schema::table('payments', function (Blueprint $table): void {
                $table->string('description')->nullable()->after('payment_method');
            });
        }
    }

    public function down(): void
    {
        // Keep this repair migration non-destructive; the column may predate it.
    }
};
