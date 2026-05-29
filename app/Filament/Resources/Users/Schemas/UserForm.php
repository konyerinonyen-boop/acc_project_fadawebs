<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use App\Models\Zone;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use spatie\Permission\Models\Role;
class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->default(null),
                TextInput::make('last_name')
                    ->default(null),
                FileUpload::make('profile_image')
                    ->image(),
                Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female'])
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('zone_id')
                    ->label('Zone')
                    ->options(Zone::pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                    Select::make('role')
                ->label('Role')
                ->options(Role::pluck('name', 'name'))
                ->searchable()
                ->afterStateHydrated(function ($set, $record) {
                    $set('role', $record?->roles->first()?->name);
                })
                ->dehydrateStateUsing(fn ($state) => $state)
                ->afterStateUpdated(function ($state, $record) {
                    if ($record) {
                        $record->syncRoles([$state]);
                    }
                }),
            ]);
    }
}
