<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // MEMBER INFO
                TextColumn::make('user.first_name')
                    ->label('Member')
                    ->searchable()
                    ->sortable(),

                // PAYMENT TYPE (IMPORTANT UPGRADE)
               TextColumn::make('paymentType.name')
            ->label('Payment Type')
                    ->color(fn (string $state): string => match ($state) {
                        'donation' => 'success',
                        'dues' => 'warning',
                        'membership' => 'primary',
                        default => 'gray',
                    }),

                // AMOUNT
                TextColumn::make('amount')
                    ->money('NGN')
                    ->sortable(),

                // STATUS
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),

                // METHOD
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->color('gray'),

                // REFERENCE
                TextColumn::make('reference')
                    ->label('Ref')
                    ->copyable()
                    ->searchable(),

                // DATE
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])

            // UI IMPROVEMENTS
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultSort('created_at', 'desc')

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
