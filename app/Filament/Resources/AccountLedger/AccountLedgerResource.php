<?php

namespace App\Filament\Resources\AccountLedger;

use App\Filament\Resources\AccountLedger\Pages\ListAccountLedgerLines;
use App\Models\JournalEntryLine;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
class AccountLedgerResource extends Resource
{
    protected static ?string $model = JournalEntryLine::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Account ledger';

    protected static ?string $modelLabel = 'journal line';

    protected static ?string $pluralModelLabel = 'account ledger';

    protected static ?int $navigationSort = 2;

    /**
     * Only posted journal entries belong in the official ledger.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['account', 'entry'])
            ->whereHas('entry', function (Builder $query) {
                $query->whereNotNull('posted_at');
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entry.entry_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('entry.reference')
                    ->label('Journal reference')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('account.code')
                    ->label('Account code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('account.name')
                    ->label('Account')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('account.type')
                    ->label('Account Type')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'asset' => 'info',
                        'liability' => 'warning',
                        'equity' => 'gray',
                        'income' => 'success',
                        'expense' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'asset' => 'Asset',
                        'liability' => 'Liability',
                        'equity' => 'Equity',
                        'income' => 'Income',
                        'expense' => 'Expense',
                        default => 'Unknown',
                    })
                    ->sortable(),

                TextColumn::make('entry.description')
                    ->label('Journal description')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('memo')
                    ->label('Line memo')
                    ->placeholder('—')
                    ->wrap(),

                TextColumn::make('debit')
                    ->label('Debit (₦)')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('credit')
                    ->label('Credit (₦)')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([15, 30, 50]);
    }

    
    public static function getPages(): array
    {
        return [
            'index' => ListAccountLedgerLines::route('/'),
        ];
    }


    public static function canViewAny(): bool
    {
        return (auth()->user()?->hasRole('super_admin') ?? false)
            && DatabaseSchema::hasTable('journal_entry_lines')
            && DatabaseSchema::hasTable('journal_entries')
            && DatabaseSchema::hasTable('ledger_accounts');
    }
}
