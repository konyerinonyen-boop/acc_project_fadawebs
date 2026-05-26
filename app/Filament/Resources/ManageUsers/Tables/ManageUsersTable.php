<?php

namespace App\Filament\Resources\ManageUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('profile_image')
                    ->label('Profile'),

                TextColumn::make('first_name')
                    ->label('Name')
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('gender'),

                TextColumn::make('membership_id')
                    ->label('Membership ID')
                    ->searchable(),

                TextColumn::make('zone.name')
                    ->label('Zone'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->formatStateUsing(fn ($state) => $state ? 'Paid' : 'Unpaid')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger'),

            ])

            ->filters([
                //
            ])

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