<?php

namespace App\Filament\Resources\BankAccounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BankAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Account For')
                    ->badge()
                    ->icon(fn (string $state): string => $state === 'donation' ? 'heroicon-m-heart' : 'heroicon-m-identification')
                    ->color(fn(string $state): string => match ($state) {
                        'membership_fee' => 'primary',
                        'donation' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('bank_name')->label('Bank')->icon('heroicon-m-building-library')->searchable()->sortable(),
                TextColumn::make('account_number')->label('Account number')->icon('heroicon-m-hashtag')->copyable()->searchable(),
                TextColumn::make('account_name')->label('Beneficiary')->icon('heroicon-m-user')->searchable(),
            ])
            ->striped()
            ->paginated([15, 30, 50])
            ->recordActions([
                EditAction::make()->icon('heroicon-m-pencil-square'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
