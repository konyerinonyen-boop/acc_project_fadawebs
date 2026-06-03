<?php

namespace App\Filament\Resources\DuesPayments\Schemas;

use App\Models\DuesPlan;
use App\Models\PaymentType;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

class DuesPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // USER
            Select::make('user_id')
                ->relationship('user', 'first_name')
                ->searchable()
                ->required(),

            // PLAN
            Select::make('dues_plan_id')
                ->relationship('duesPlan', 'name')
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {

                    $plan = DuesPlan::find($state);

                    if ($plan) {
                        $set('amount', $plan->monthly_amount);
                    }
                }),

            // ✅ REAL AMOUNT (NOW STORED)
            TextInput::make('amount')
                ->numeric()
                ->disabled()
                ->dehydrated() // IMPORTANT: still saves to DB
                ->required(),

            // PAYMENT METHOD
            Select::make('payment_method')
                ->label('Payment Method')
                ->options([
                    'transfer' => 'Bank Transfer',
                    'card' => 'Card',
                    'paystack' => 'Paystack',
                ])
                ->required(),

            // PAYMENT TYPE
            Select::make('payment_type_id')
                ->relationship('paymentType', 'name')
                ->label('Payment Type')
                ->options(
                    PaymentType::whereIn('name', ['dues', 'membership'])
                        ->pluck('name', 'id')
                )
                ->required(),

            // MONTHS PAID
            TextInput::make('months_paid')
                ->numeric()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {

                    $start = $get('coverage_start') ?? now();
                    $set('coverage_start', $start);

                    $set('coverage_end',
                        \Carbon\Carbon::parse($start)->addMonths((int) $state)
                    );
                }),

            // START DATE
            DatePicker::make('coverage_start')
                ->default(now())
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {

                    $months = $get('months_paid');

                    if ($state && $months) {
                        $set('coverage_end',
                            \Carbon\Carbon::parse($state)->addMonths($months)
                        );
                    }
                })
                ->required(),

            // END DATE
            DatePicker::make('coverage_end')
                ->disabled()
                ->dehydrated()
                ->required(),
        ]);
    }
}
