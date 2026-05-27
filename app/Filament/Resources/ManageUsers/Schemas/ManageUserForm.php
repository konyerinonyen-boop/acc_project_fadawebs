<?php

namespace App\Filament\Resources\ManageUsers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ManageUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                FileUpload::make('profile_image')
                    ->image()
                    ->directory('members'),

                TextInput::make('first_name')
                    ->label('Full Name')
                    ->required(),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->dehydrated(fn ($state) => filled($state)),

                TextInput::make('phone')
                    ->tel(),

                Select::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),

                // SUPER ADMIN CAN SELECT ANY ZONE
                Select::make('zone_id')
                    ->relationship('zone', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()->hasRole('super_admin'))
                    ->required(fn () => auth()->user()->hasRole('super_admin')),

                // ZONAL ADMIN ONLY SEES THEIR ZONE (READ ONLY)
                TextInput::make('zone_name')
                    ->label('Zone')
                    ->default(fn () => auth()->user()->zone?->name)
                    ->disabled()
                    ->visible(fn () => auth()->user()->hasRole('zonal_admin'))
                    ->dehydrated(false),

                TextInput::make('membership_id')
                    ->default(fn () => 'MEM-' . rand(10000, 99999))
                    ->disabled()
                    ->dehydrated()
                    ->unique(ignoreRecord: true),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'suspended' => 'Suspended',
                    ])
                    ->default('pending')
                    ->required(),

                Select::make('payment_status')
                    ->options([
                        1 => 'Paid',
                        0 => 'Unpaid',
                    ])
                    ->default(0)
                    ->required(),

            ]);
    }
}