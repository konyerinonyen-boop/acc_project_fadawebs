<?php

namespace App\Filament\Resources\ManageUsers;

use App\Filament\Resources\ManageUsers\Pages\CreateManageUser;
use App\Filament\Resources\ManageUsers\Pages\EditManageUser;
use App\Filament\Resources\ManageUsers\Pages\ListManageUsers;
use App\Filament\Resources\ManageUsers\Pages\ViewManageUser;
use App\Filament\Resources\ManageUsers\Schemas\ManageUserForm;
use App\Filament\Resources\ManageUsers\Schemas\ManageUserInfolist;
use App\Filament\Resources\ManageUsers\Tables\ManageUsersTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ManageUserResource extends Resource
{

    protected static ?string $model = User::class;

   protected static string|\UnitEnum|null $navigationGroup = 'Member Management';

     protected static ?string $navigationLabel = ' Manage members';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;
    protected static ?string $recordTitleAttribute = 'User';

    protected static ?string $modelLabel = 'Member';
    public static function form(Schema $schema): Schema
    {
        return ManageUserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ManageUserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ManageUsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        $query = parent::getEloquentQuery()
            ->role('member');

        // Super admin sees all
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // Zonal admin sees only their zone
        return $query->where('zone_id', $user->zone_id);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListManageUsers::route('/'),
            'create' => CreateManageUser::route('/create'),
            'view' => ViewManageUser::route('/{record}'),
            'edit' => EditManageUser::route('/{record}/edit'),
        ];
    }

}
