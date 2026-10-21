<?php

namespace App\Filament\Resources\DuesPayments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DuesPaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')->label('Member'),
                TextEntry::make('duesPlan.name')->label('Dues plan')->placeholder('—'),
                TextEntry::make('payment.amount')->label('Payment amount')->money('NGN')->placeholder('—'),
                TextEntry::make('payment.reference')->label('Payment reference')->copyable()->placeholder('—'),
                TextEntry::make('payment.status')->label('Payment status')->badge(),
                TextEntry::make('months_paid')->label('Months covered'),
                TextEntry::make('coverage_start')->date()->placeholder('—'),
                TextEntry::make('coverage_end')->date()->placeholder('—'),
                TextEntry::make('created_at')->label('Recorded')->dateTime(),
            ])->columns(2);
    }
}
