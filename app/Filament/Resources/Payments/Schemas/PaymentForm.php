<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
               Select::make('user_id')
                    ->relationship('user', 'first_name')
                    ->searchable()
                    ->required(),

                TextInput::make('amount')
                    ->numeric()
                    ->required(),

                TextInput::make('reference')
                    ->default(fn () => 'PAY-' . strtoupper(uniqid()))
                    ->disabled(),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'suspended' => 'Suspended',
                    ])
                    ->default('pending')
                    ->required(),

                Select::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'transfer' => 'Bank Transfer',
                        'card' => 'Card',
                        'paystack' => 'Paystack',
                    ]),
                 Select::make('description')
                    ->options([
                       'membership_fee' => 'Membership Fee',
                       'donation' => 'Donation',
                    ]),
            ]);
    }
}
