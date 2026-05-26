<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PermissionTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
           TextColumn::make('group')
                ->badge()
                ->sortable(),

            TextColumn::make('name')
                ->searchable()
                ->sortable(),
        ]);
    }
}