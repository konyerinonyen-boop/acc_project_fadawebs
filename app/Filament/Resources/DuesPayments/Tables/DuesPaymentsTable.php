<?php

namespace App\Filament\Resources\DuesPayments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DuesPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('user.first_name')
                    ->label('Member')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('duesPlan.name')
                    ->label('Plan')
                    ->badge(),

                // PAYMENT AMOUNT (FIXED)
                TextColumn::make('payment.amount')
                    ->label('Amount')
                    ->money('NGN')
                    ->sortable(),

                TextColumn::make('months_paid')
                    ->label('Months')
                    ->badge(),

                TextColumn::make('coverage_start')
                    ->date()
                    ->sortable(),

                TextColumn::make('coverage_end')
                    ->date()
                    ->sortable(),

                // PAYMENT STATUS (FIXED)
                TextColumn::make('payment.status')
                    ->label('Payment Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),

                // PAYMENT REFERENCE (FIXED)
                TextColumn::make('payment.reference')
                    ->label('Reference')
                    ->searchable()
                    ->copyable(),
            ])

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