<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class RoleTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                
                TextColumn::make('name')
                    ->label('Role')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('permissions.name')
                    ->label('Permissions')
                    ->badge()
,
            ]);
    }
}