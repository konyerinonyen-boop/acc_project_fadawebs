<?php

namespace App\Filament\Resources\DuesPlans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DuesPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('name')
                ->label('Plan Name')
                ->placeholder('e.g., Basic Plan')
                ->required(),

            TextInput::make('monthly_amount')
                ->numeric()
                ->required(),

            Toggle::make('is_active')
                ->default(true),
        ]);
    }
}
