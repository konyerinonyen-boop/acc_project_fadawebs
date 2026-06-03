<?php

namespace App\Filament\Resources\ManageUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

                TextColumn::make('months_since_join')
                    ->label('Months Since Join')
                    ->state(fn ($record) => $record->monthsSinceJoin())
                    ->toggleable(),

 TextColumn::make('months_paid')
    ->label('Months Paid')
    ->state(fn ($record) => $record->monthsPaid()),

TextColumn::make('months_owing')
    ->label('Months Owing')
    ->state(fn ($record) => $record->monthsOwing()),
                TextColumn::make('zone.name')
                    ->label('Zone')
                     ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),

              TextColumn::make('membership_payment')
    ->label('Payment')
    ->badge()
    ->getStateUsing(fn ($record) =>
        $record->monthsOwing() > 0 ? 'Unpaid' : 'Paid'
    )
    ->color(fn ($state) =>
        $state === 'Paid' ? 'success' : 'danger'
    ),

               TextColumn::make('membership_status')
    ->label('Membership Status')
    ->badge()
    ->state(fn ($record) =>
        $record->monthsOwing() > 0
            ? $record->monthsOwing() . ' Month(s) Owing'
            : 'Up to Date'
    )
    ->color(fn ($record) =>
        $record->monthsOwing() > 0
            ? 'danger'
            : 'success'
    ),
            ])

            // ->filters([

            //     // ✅ PAID MEMBERS
            // Filter::make('up_to_date')
            //     ->label('Up to Date')
            //     ->query(fn (Builder $query) =>
            //         $query->get()->filter(fn ($user) =>
            //             $user->monthsOwing() === 0
            //         )
            //     ),

            //     // ❌ OWING MEMBERS
            // Filter::make('owing')
            //     ->label('Owing Members')
            //     ->query(fn (Builder $query) =>
            //         $query->get()->filter(fn ($user) =>
            //             $user->monthsOwing() > 0
            //         )
            //     ),

            // ])

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
