<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('title')
                    ->required(),

                FileUpload::make('image')
                    ->image(),
                 Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('venue')
                    ->required(),

                     DateTimePicker::make('date')
                            ->label('Date')
                            ->required(),

                    Select::make('zone_id')
                    ->relationship('zone', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()->hasRole('super_admin'))
                    ->required(fn () => auth()->user()->hasRole('super_admin')),

                // ZONAL ADMIN ONLY SEES THEIR ZONE
                TextInput::make('zone_name')
                    ->label('Zone')
                    ->default(fn () => auth()->user()->zone?->name)
                    ->disabled()
                    ->visible(fn () => auth()->user()->hasRole('zonal_admin'))
                    ->dehydrated(false),
            ]);
    }
}