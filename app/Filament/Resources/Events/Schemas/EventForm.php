<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event overview')
                    ->description('Set the event name, location, and the details attendees will see.')
                    ->icon('heroicon-o-calendar')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Event title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('venue')
                            ->label('Venue')
                            ->prefixIcon('heroicon-m-map-pin')
                            ->required()
                            ->maxLength(255),
                        DateTimePicker::make('date')
                            ->label('Date and time')
                            ->native(false)
                            ->required(),
                        Textarea::make('description')
                            ->label('Event description')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('Event image')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('events')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
                Section::make('Visibility and ownership')
                    ->description('Choose whether to feature this event and assign it to the right zone.')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_featured')
                            ->label('Feature this event')
                            ->helperText('Featured events receive extra visibility on the homepage.'),
                        Select::make('zone_id')
                            ->label('Zone')
                            ->relationship('zone', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(fn () => auth()->user()->hasRole('super_admin'))
                            ->required(fn () => auth()->user()->hasRole('super_admin')),
                        TextInput::make('zone_name')
                            ->label('Zone')
                            ->prefixIcon('heroicon-m-map')
                            ->default(fn () => auth()->user()->zone?->name)
                            ->disabled()
                            ->visible(fn () => auth()->user()->hasRole('zonal_admin'))
                            ->dehydrated(false),
                    ]),
            ]);
    }
}