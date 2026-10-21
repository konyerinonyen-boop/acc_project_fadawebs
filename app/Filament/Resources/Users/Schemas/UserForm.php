<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use App\Models\Zone;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use spatie\Permission\Models\Role;
class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                    Section::make('Member profile')
                        ->description('Personal details and contact information for this account.')
                        ->icon('heroicon-o-identification')
                        ->columns(2)
                        ->schema([
                            TextInput::make('first_name')
                                ->label('First name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('last_name')
                                ->label('Last name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->label('Email address')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            Select::make('gender')
                                ->options(['male' => 'Male', 'female' => 'Female'])
                                ->native(false),
                            Select::make('zone_id')
                                ->label('Zone')
                                ->options(fn () => Zone::query()->orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->native(false),
                            FileUpload::make('profile_image')
                                ->label('Profile photo')
                                ->image()
                                ->imageEditor()
                                ->disk('public')
                                ->directory('profiles')
                                ->visibility('public')
                                ->columnSpanFull(),
                        ]),
                    Section::make('Account access')
                        ->description('Manage the sign-in password and access level.')
                        ->icon('heroicon-o-shield-check')
                        ->columns(2)
                        ->schema([
                            TextInput::make('password')
                                ->password()
                                ->revealable()
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->dehydrated(fn ($state): bool => filled($state))
                                ->minLength(8)
                                ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Leave blank to keep the current password.' : null),
                            DateTimePicker::make('email_verified_at')
                                ->label('Email verified at')
                                ->native(false),
                            Select::make('status')
                                ->label('Member status')
                                ->options([
                                    'pending' => 'Pending review',
                                    'approved' => 'Approved',
                                    'suspended' => 'Suspended',
                                ])
                                ->default('pending')
                                ->native(false)
                                ->prefixIcon('heroicon-m-shield-check')
                                ->required(),
                            Select::make('role')
                                ->label('Role')
                                ->options(fn () => Role::query()->orderBy('name')->pluck('name', 'name'))
                                ->searchable()
                                ->native(false)
                                ->prefixIcon('heroicon-m-key')
                                ->afterStateHydrated(function ($set, $record) {
                                    $set('role', $record?->roles->first()?->name);
                                })
                                ->dehydrateStateUsing(fn ($state) => $state)
                                ->afterStateUpdated(function ($state, $record) {
                                    if ($record) {
                                        $record->syncRoles([$state]);
                                    }
                                }),
                        ]),
            ]);
    }
}
