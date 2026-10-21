<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('type');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date');
            $table->string('reference')->unique();
            $table->string('description');
            $table->string('source_type', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unique(['source_type', 'source_id']);
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ledger_account_id')->constrained()->restrictOnDelete();
            $table->decimal('debit', 14, 2)->default(0);
            $table->decimal('credit', 14, 2)->default(0);
            $table->string('memo')->nullable();
            $table->timestamps();
        });

        DB::table('ledger_accounts')->insert([
            ['code' => '1000', 'name' => 'Cash and bank', 'type' => 'asset', 'description' => 'Funds received and held by the organisation.', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '2000', 'name' => 'Accounts payable', 'type' => 'liability', 'description' => 'Amounts owed by the organisation.', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '3000', 'name' => 'Accumulated fund', 'type' => 'equity', 'description' => 'Opening and accumulated organisation funds.', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '4000', 'name' => 'Membership dues income', 'type' => 'income', 'description' => 'Member dues and subscriptions.', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '4100', 'name' => 'Donations income', 'type' => 'income', 'description' => 'Donations received.', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '5000', 'name' => 'Operating expenses', 'type' => 'expense', 'description' => 'General operating costs.', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('ledger_accounts');
    }
};
