<?php

namespace App\Filament\Resources\DuesPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class DuesPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Plan')
                    ->icon('heroicon-m-tag')
                    ->weight('semibold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('monthly_amount')
                    ->label('Monthly amount')
                    ->money('NGN'),
                IconColumn::make('is_active')
                    ->label('Available')
                    ->boolean()
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-pause-circle')
                    ->sortable(),
            ])
            ->defaultSort('name')
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
