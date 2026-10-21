<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-calendar-days')
                    ->weight('semibold'),
                TextColumn::make('venue')
                    ->searchable()
                    ->icon('heroicon-m-map-pin'),

                TextColumn::make('zone.name')
                    ->label('Zone')
                    ->badge(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-m-star')
                    ->falseIcon('heroicon-m-minus')
                    ->sortable(),

                TextColumn::make('schedules_count')
                    ->counts('schedules')
                    ->label('Schedules')
                    ->badge(),

                TextColumn::make('speakers_count')
                    ->counts('speakers')
                    ->label('Speakers')
                    ->badge(),
                TextColumn::make('attendances_count')
                    ->counts('attendances')
                    ->label('Attendance')
                    ->badge(),

                TextColumn::make('user.first_name')
                    ->label('Created By')
                    ->searchable(),

                TextColumn::make('date')
                    ->dateTime('M j, Y · g:i A')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_featured')
                    ->label('Featured events'),
            ])
            ->defaultSort('date', 'desc')
            ->recordActions([
                ViewAction::make()->icon('heroicon-m-eye'),
                EditAction::make()->icon('heroicon-m-pencil-square'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->recordAction(ViewAction::class);
    }
}
