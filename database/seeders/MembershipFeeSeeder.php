<?php

namespace Database\Seeders;

use App\Models\MembershipFee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MembershipFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MembershipFee::create([
            'name' => 'Monthly',
            'amount' => 5000,
            'duration_days' => 30,
        ]);

         MembershipFee::create([
            'name' => '3 Monthly',
            'amount' => 15000,
            'duration_days' => 90,
        ]);

         MembershipFee::create([
            'name' => 'Monthly',
            'amount' => 60000,
            'duration_days' => 365,
        ]);
    }
}
