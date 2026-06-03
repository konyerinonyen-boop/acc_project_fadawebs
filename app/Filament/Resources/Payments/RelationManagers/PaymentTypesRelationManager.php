<?php

namespace App\Filament\Resources\Payments\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PaymentTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'types';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),

            Forms\Components\Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(fn ($state) => "
                        <div class='px-2 py-1 rounded bg-gray-100 text-gray-700 text-sm'>
                            {$state}
                        </div>
                    ")
                    ->html(),

                Tables\Columns\TextColumn::make('is_active')
                    ->formatStateUsing(fn ($state) => "
                        <div class='px-2 py-1 rounded text-sm " .
                            ($state ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') .
                        "'>
                            " . ($state ? 'Active' : 'Inactive') . "
                        </div>
                    ")
                    ->html(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
