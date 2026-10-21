<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payment details')
                ->description('Choose the member and record the amount and payment category.')
                ->icon('heroicon-o-credit-card')
                ->columns(2)
                ->schema([
                    Select::make('user_id')
                        ->label('Member')
                        ->relationship('user', 'first_name')
                        ->searchable(['first_name', 'last_name', 'email'])
                        ->preload()
                        ->required(),
                    Select::make('description')
                        ->label('Payment type')
                        ->options([
                            'membership_fee' => 'Membership fee',
                            'donation' => 'Donation',
                        ])
                        ->native(false)
                        ->required(),
                    TextInput::make('amount')
                        ->prefix('₦')
                        ->numeric()
                        ->minValue(0.01)
                        ->step('0.01')
                        ->required(),
                    Select::make('payment_method')
                        ->label('Payment method')
                        ->options([
                            'transfer' => 'Bank transfer',
                            'card' => 'Card',
                            'paystack' => 'Paystack',
                        ])
                        ->native(false)
                        ->nullable(),
                ]),
            Section::make('Review and reference')
                ->description('References are generated automatically. Set the status after reviewing proof of payment.')
                ->icon('heroicon-o-clipboard-document-check')
                ->columns(2)
                ->schema([
                    TextInput::make('reference')
                        ->default(fn () => 'PAY-' . strtoupper(uniqid()))
                        ->prefixIcon('heroicon-m-hashtag')
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                    Select::make('status')
                        ->options([
                            'pending' => 'Pending review',
                            'approved' => 'Approved',
                            'suspended' => 'Suspended',
                        ])
                        ->default('pending')
                        ->native(false)
                        ->required(),
                ]),
        ]);
    }
}
