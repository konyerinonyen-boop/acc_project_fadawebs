<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_image')
                    ->disk('public')
                    ->circular()
                    ->label('Photo'),
                TextColumn::make('name')
                    ->label('Member')
                    ->icon('heroicon-m-user-circle')
                    ->weight('semibold')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(query: fn ($query, string $direction) => $query
                        ->orderBy('first_name', $direction)
                        ->orderBy('last_name', $direction)),
                TextColumn::make('gender')
                    ->badge()
                    ->icon(fn (?string $state): string => $state === 'female' ? 'heroicon-m-sparkles' : 'heroicon-m-user'),
                TextColumn::make('email')
                    ->label('Email address')
                    ->icon('heroicon-m-envelope')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('membership_id')
                    ->label('Membership ID')
                    ->icon('heroicon-m-identification')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color('primary')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (?string $state): string => match ($state) {
                        'approved' => 'heroicon-m-check-circle',
                        'pending' => 'heroicon-m-clock',
                        'suspended' => 'heroicon-m-no-symbol',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),
                TextColumn::make('email_verified_at')
                    ->label('Email verified')
                    ->dateTime()
                    ->placeholder('Not verified')
                    ->sortable(),
                TextColumn::make('zone.name')
                    ->label('Zone')
                    ->icon('heroicon-m-map')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female']),
                SelectFilter::make('zone_id')
                    ->label('Zone')
                    ->relationship('zone', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->deferFilters()
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()->icon('heroicon-m-eye'),
                EditAction::make()->icon('heroicon-m-pencil-square'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
