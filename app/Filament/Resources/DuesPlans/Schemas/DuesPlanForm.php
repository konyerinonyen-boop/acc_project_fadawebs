<?php

namespace App\Filament\Resources\DuesPlans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DuesPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dues plan')
                ->description('Set the plan name and recurring member contribution.')
                ->icon('heroicon-o-rectangle-stack')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Plan name')
                        ->placeholder('e.g. Standard membership')
                        ->prefixIcon('heroicon-m-tag')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('monthly_amount')
                        ->label('Monthly amount')
                        ->prefix('₦')
                        ->numeric()
                        ->minValue(0.01)
                        ->step('0.01')
                        ->required(),
                    Toggle::make('is_active')
                        ->label('Available for new payments')
                        ->default(true)
                        ->helperText('Inactive plans remain on historical records but cannot be selected for new dues.')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
