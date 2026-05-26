<?php

namespace App\Filament\Resources\Zones\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            TextInput::make('user.first_name')
                ->label('Name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('name', $state);
                }),

            TextInput::make('name')
                ->label('Zone Name')
                ->disabled()
                ->dehydrated() // still saved to DB
                ->required(),

                Select::make('stuatus')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active')
                    ->required(),

                FileUpload::make('user.profile_image')
                    ->label('Profile Image')
                    ->image()
                    ->directory('profile-images'),

                TextInput::make('user.email')
                    ->label('Email Address')
                   ->email()
                    ->required(),
                TextInput::make('role')
                    ->label('Role')
                    ->default('zonal_admin')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('user.email_verified_at')
                    ->label('Email Verified At')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn ($record) => $record !== null),

                TextInput::make('user.created_at')
                    ->label('Created At')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn ($record) => $record !== null),

                    TextInput::make('user.password')
                    ->password()
                    ->required(),
                TextInput::make('user.updated_at')
                    ->label('Updated At')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn ($record) => $record !== null),

            ]);
    }
}