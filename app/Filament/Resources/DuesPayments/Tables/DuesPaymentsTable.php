<?php

namespace App\Filament\Resources\DuesPayments\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DuesPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('user.name')
                    ->label('Member')
                    ->icon('heroicon-m-user-circle')
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
                    ->badge()
                    ->color('primary'),

                TextColumn::make('coverage_start')
                    ->label('Coverage starts')
                    ->date()
                    ->sortable(),

                TextColumn::make('coverage_end')
                    ->label('Coverage ends')
                    ->date()
                    ->sortable(),

                // PAYMENT STATUS (FIXED)
                TextColumn::make('payment.status')
                    ->label('Payment Status')
                    ->badge()
                    ->icon(fn (?string $state): string => match ($state) {
                        'pending' => 'heroicon-m-clock',
                        'approved' => 'heroicon-m-check-circle',
                        'suspended' => 'heroicon-m-no-symbol',
                        default => 'heroicon-m-question-mark-circle',
                    })
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
                    ->copyable()
                    ->icon('heroicon-m-clipboard-document'),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Payment status')
                    ->options([
                        'pending' => 'Pending review',
                        'approved' => 'Approved',
                        'suspended' => 'Suspended',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, string $status): Builder => $query->whereHas(
                            'payment',
                            fn (Builder $query) => $query->where('status', $status),
                        ),
                    )),
            ])
            ->deferFilters()
            ->paginated([10, 25, 50])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()->icon('heroicon-m-eye'),
                EditAction::make()->icon('heroicon-m-pencil-square'),
            ])
            ->recordAction(ViewAction::class);
    }
}
