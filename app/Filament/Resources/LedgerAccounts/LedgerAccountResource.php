<?php

namespace App\Filament\Resources\LedgerAccounts;

use App\Filament\Resources\LedgerAccounts\Pages\CreateLedgerAccount;
use App\Filament\Resources\LedgerAccounts\Pages\EditLedgerAccount;
use App\Filament\Resources\LedgerAccounts\Pages\ListLedgerAccounts;
use App\Models\LedgerAccount;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema as DatabaseSchema;

class LedgerAccountResource extends Resource
{
    protected static ?string $model = LedgerAccount::class;
    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Chart of accounts';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')->label('Account code')->required()->maxLength(20)->unique(ignoreRecord: true),
            TextInput::make('name')->required()->maxLength(255),
            Select::make('type')->options(['asset' => 'Asset', 'liability' => 'Liability', 'equity' => 'Equity', 'income' => 'Income', 'expense' => 'Expense'])->required()->native(false),
            Textarea::make('description')->rows(3)->columnSpanFull(),
            Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->label('Code')->sortable()->searchable()->weight('bold'),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('type')->badge()->color(fn (string $state) => match ($state) { 'asset' => 'info', 'liability' => 'warning', 'equity' => 'gray', 'income' => 'success', 'expense' => 'danger', default => 'gray' })->sortable(),
            TextColumn::make('lines_count')->counts('lines')->label('Journal lines'),
            IconColumn::make('is_active')->boolean()->label('Active'),
        ])->filters([SelectFilter::make('type')->options(['asset' => 'Asset', 'liability' => 'Liability', 'equity' => 'Equity', 'income' => 'Income', 'expense' => 'Expense'])])
            ->defaultSort('code')->striped()->paginated([15, 30, 50]);
    }

    public static function getPages(): array
    {
        return ['index' => ListLedgerAccounts::route('/'), 'create' => CreateLedgerAccount::route('/create'), 'edit' => EditLedgerAccount::route('/{record}/edit')];
    }

    public static function canViewAny(): bool
    {
        return (auth()->user()?->hasRole('super_admin') ?? false)
            && DatabaseSchema::hasTable('ledger_accounts');
    }
}
