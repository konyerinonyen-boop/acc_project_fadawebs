<?php

namespace App\Filament\Resources\ResourceMaterials;

use App\Filament\Resources\ResourceMaterials\Pages\CreateResourceMaterial;
use App\Filament\Resources\ResourceMaterials\Pages\EditResourceMaterial;
use App\Filament\Resources\ResourceMaterials\Pages\ListResourceMaterials;
use App\Filament\Resources\ResourceMaterials\Pages\ViewResourceMaterial;
use App\Filament\Resources\ResourceMaterials\Schemas\ResourceMaterialForm;
use App\Filament\Resources\ResourceMaterials\Schemas\ResourceMaterialInfolist;
use App\Filament\Resources\ResourceMaterials\Tables\ResourceMaterialsTable;
use App\Models\ResourceMaterial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

class ResourceMaterialResource extends Resource
{
    protected static ?string $model = ResourceMaterial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'ResourceMaterial';

    public static function form(Schema $schema): Schema
    {
        return ResourceMaterialForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ResourceMaterialInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResourceMaterialsTable::configure($table);
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
            'index' => ListResourceMaterials::route('/'),
            'create' => CreateResourceMaterial::route('/create'),
            'view' => ViewResourceMaterial::route('/{record}'),
            'edit' => EditResourceMaterial::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('super_admin');
    }

    
    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('super_admin');
    }

  
    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('super_admin');
    }
}
