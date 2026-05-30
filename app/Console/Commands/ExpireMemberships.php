<?php

namespace App\Console\Commands;

use App\Models\Membership;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Command;

#[Signature('memberships:expire')]
#[Description('Expire memberships automatically')]
class ExpireMemberships extends Command
{
    public function handle()
    {
        $count = Membership::where('status', 'active')
            ->where('expiry_date', '<', now())
            ->update([
                'status' => 'expired'
            ]);

        $this->info("Expired memberships: " . $count);
    }
}