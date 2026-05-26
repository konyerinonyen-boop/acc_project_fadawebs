<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Roles\Schemas\RoleTable;
use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Models\Role;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class RoleResource extends Resource
{
    protected static ?string $model = \Spatie\Permission\Models\Role::class;

    /** @var string|\BackedEnum|null */
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoleTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }
}