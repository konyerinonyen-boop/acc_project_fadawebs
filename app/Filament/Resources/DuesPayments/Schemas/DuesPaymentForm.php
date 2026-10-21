<?php

namespace App\Filament\Resources\DuesPayments\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;

class DuesPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Member and payment')
                ->description('Link this coverage record to the member and the approved payment that funded it.')
                ->icon('heroicon-o-user-circle')
                ->columns(2)
                ->schema([
                    Select::make('user_id')
                        ->label('Member')
                        ->relationship('user', 'first_name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('dues_plan_id')
                        ->label('Dues plan')
                        ->relationship('duesPlan', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('payment_id')
                        ->label('Funding payment')
                        ->relationship('payment', 'reference')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpanFull(),
                ]),
            Section::make('Coverage period')
                ->description('The end date is calculated automatically from the start date and number of months.')
                ->icon('heroicon-o-calendar-days')
                ->columns(3)
                ->schema([
                    TextInput::make('months_paid')
                        ->label('Months covered')
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $start = $get('coverage_start') ?? now();
                            $set('coverage_start', $start);
                            $set('coverage_end', \Carbon\Carbon::parse($start)->addMonths(max(0, (int) $state - 1)));
                        }),
                    DatePicker::make('coverage_start')
                        ->label('Coverage starts')
                        ->default(now())
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $months = $get('months_paid');

                            if ($state && $months) {
                                $set('coverage_end', \Carbon\Carbon::parse($state)->addMonths(max(0, (int) $months - 1)));
                            }
                        })
                        ->required(),
                    DatePicker::make('coverage_end')
                        ->label('Coverage ends')
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                ]),
        ]);
    }
}
