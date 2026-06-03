<?php

namespace App\Filament\Resources\DuesPlans;

use App\Filament\Resources\DuesPlans\Pages\CreateDuesPlan;
use App\Filament\Resources\DuesPlans\Pages\EditDuesPlan;
use App\Filament\Resources\DuesPlans\Pages\ListDuesPlans;
use App\Filament\Resources\DuesPlans\Pages\ViewDuesPlan;
use App\Filament\Resources\DuesPlans\Schemas\DuesPlanForm;
use App\Filament\Resources\DuesPlans\Schemas\DuesPlanInfolist;
use App\Filament\Resources\DuesPlans\Tables\DuesPlansTable;
use App\Models\DuesPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DuesPlanResource extends Resource
{
    protected static ?string $model = DuesPlan::class;

     protected static string|\UnitEnum|null $navigationGroup = 'Dues Management';

        protected static ?string $navigationLabel = ' Dues Plans';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'DuesPlan';

    public static function form(Schema $schema): Schema
    {
        return DuesPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DuesPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DuesPlansTable::configure($table);
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
            'index' => ListDuesPlans::route('/'),
            'create' => CreateDuesPlan::route('/create'),
            'view' => ViewDuesPlan::route('/{record}'),
            'edit' => EditDuesPlan::route('/{record}/edit'),
        ];
    }

     public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }
}
