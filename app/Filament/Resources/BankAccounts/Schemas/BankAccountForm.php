<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bank account details')
                    ->description('Add the receiving account members should use for this payment category.')
                    ->icon('heroicon-o-building-library')
                    ->columns(2)
                    ->schema([
                        Select::make('type')
                            ->label('Account for')
                            ->options([
                                'membership_fee' => 'Membership fee',
                                'donation' => 'Donation',
                            ])
                            ->native(false)
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('bank_name')
                            ->label('Bank name')
                            ->prefixIcon('heroicon-m-building-library')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('account_number')
                            ->label('Account number')
                            ->prefixIcon('heroicon-m-hashtag')
                            ->required()
                            ->length(10),
                        TextInput::make('account_name')
                            ->label('Account name')
                            ->prefixIcon('heroicon-m-user')
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }
}
