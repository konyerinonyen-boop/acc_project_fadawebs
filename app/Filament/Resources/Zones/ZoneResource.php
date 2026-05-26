<?php

namespace App\Filament\Resources\Zones;

use App\Filament\Resources\Zones\Pages\CreateZone;
use App\Filament\Resources\Zones\Pages\EditZone;
use App\Filament\Resources\Zones\Pages\ListZones;
use App\Filament\Resources\Zones\Schemas\ZoneForm;
use App\Filament\Resources\Zones\Tables\ZonesTable;
use App\Models\Zone;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ZoneResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Zonal Management';

    protected static ?string $model = Zone::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::GlobeEuropeAfrica;
     protected static ?string $navigationLabel = ' Manage Zones';

    protected static ?string $recordTitleAttribute = 'zone';

     public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return ZoneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ZonesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListZones::route('/'),
            'create' => CreateZone::route('/create'),
            'edit' => EditZone::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }

   
}