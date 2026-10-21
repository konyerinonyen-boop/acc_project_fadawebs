<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('payments', function (Blueprint $table): void {
        $table->id();
        $table->string('payment_method')->nullable();
    });
});

afterEach(function () {
    Schema::dropIfExists('payments');
});

test('payment description repair migration adds a missing nullable column and is safe to rerun', function () {
    $migration = require database_path('migrations/2026_10_21_132100_ensure_payments_description_column.php');

    $migration->up();
    $migration->up();

    expect(Schema::hasColumn('payments', 'description'))->toBeTrue();
});
