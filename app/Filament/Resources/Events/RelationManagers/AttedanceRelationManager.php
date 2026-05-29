<?php

namespace App\Filament\Resources\Events\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Schemas\Schema;

use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $recordTitleAttribute = 'first_name';

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('first_name')
                ->required(),

            TextInput::make('last_name')
                ->required(),

            TextInput::make('phone_number')
                ->tel()
                ->required(),

            TextInput::make('email')
                ->email()
                ->required(),

            Textarea::make('home_address')
                ->rows(3),

            Select::make('gender')
                ->options([
                    'male' => 'Male',
                    'female' => 'Female',
                ])
                ->required(),

        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('attendance_id')
                    ->label('Attendance ID')
                    ->searchable(),

                TextColumn::make('first_name')
                    ->searchable(),

                TextColumn::make('last_name')
                    ->searchable(),

                TextColumn::make('phone_number'),

                TextColumn::make('email'),

                TextColumn::make('gender')
                    ->badge(),

            ])
            ->headerActions([
                CreateAction::make('attendance_id')
                ->label('Add Attendance')
                ->mutateFormDataUsing(function (array $data): array {

                    $lastId = \App\Models\Attendance::max('id') + 100;

                    $data['attendance_id'] = 'ATT' . $lastId;

                    return $data;
                }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}