<?php

namespace App\Filament\Resources\Events\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;


class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $recordTitleAttribute = 'title';

    // ✅ IMPORTANT: use Schema (NOT Form)
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),
            TextInput::make('speaker')->required(),
            TextInput::make('venue')->required(),

            DateTimePicker::make('time')->required(),

            TextInput::make('duration')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('speaker'),
                TextColumn::make('venue'),
                TextColumn::make('time')->dateTime(),
                TextColumn::make('duration'),
            ])
            ->headerActions([
                CreateAction::make()->label('Add Schedule'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}