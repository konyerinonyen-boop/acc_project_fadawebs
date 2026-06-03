<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // USER
            Select::make('user_id')
                ->relationship('user', 'first_name')

                ->required(),

            // PAYMENT TYPE (VERY IMPORTANT)
            Select::make('payment_type_id')
                ->relationship('paymentType', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->createOptionForm([
                    TextInput::make('name')
                        ->required(),
                ]),

            // AMOUNT
            TextInput::make('amount')
                ->numeric()
                ->required(),

            // REFERENCE (FIXED)
            TextInput::make('reference')
                ->default(fn () => 'PAY-' . strtoupper(uniqid()))
                ->disabled()
                ->dehydrated()
                ->required(),

            // STATUS
            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'suspended' => 'Suspended',
                ])
                ->default('pending')
                ->required(),

            // PAYMENT METHOD
            Select::make('payment_method')
                ->options([
                    'transfer' => 'Bank Transfer',
                    'card' => 'Card',
                    'paystack' => 'Paystack',
                ])
                ->nullable(),

        ]);
    }
}
