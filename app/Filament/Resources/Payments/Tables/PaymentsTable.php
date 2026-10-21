<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('user.name')
                    ->label('Member')
                    ->icon('heroicon-m-user-circle')
                    ->searchable(['user.first_name', 'user.last_name'])
                    ->sortable(query: fn ($query, string $direction) => $query
                        ->join('users', 'users.id', '=', 'payments.user_id')
                        ->orderBy('users.first_name', $direction)
                        ->select('payments.*')),

                TextColumn::make('description')
                    ->label('Payment type')
                    ->badge()
                    ->icon(fn (?string $state): string => $state === 'donation' ? 'heroicon-m-heart' : 'heroicon-m-identification')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'donation' => 'Donation',
                        'membership_fee' => 'Membership fee',
                        default => str($state)->headline()->toString(),
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'donation' => 'success',
                        'membership_fee' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('amount')
                    ->money('NGN')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
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

                // METHOD
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('reference')
                    ->label('Reference')
                    ->copyable()
                    ->searchable()
                    ->icon('heroicon-m-clipboard-document')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, Y · g:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending review',
                        'approved' => 'Approved',
                        'suspended' => 'Suspended',
                    ]),
                SelectFilter::make('description')
                    ->label('Payment type')
                    ->options([
                        'membership_fee' => 'Membership fee',
                        'donation' => 'Donation',
                    ]),
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
