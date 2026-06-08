<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('venue')
                    ->searchable(),

                TextColumn::make('zone.name')
                    ->label('Zone')
                    ->badge(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
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
                    ->label('attendances')
                    ->badge(),

                TextColumn::make('user.first_name')
                    ->label('Created By')
                    ->searchable(),

                TextColumn::make('date')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),

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
