<?php

namespace App\Filament\Resources\JournalEntries;

use App\Filament\Resources\JournalEntries\Pages\CreateJournalEntry;
use App\Filament\Resources\JournalEntries\Pages\EditJournalEntry;
use App\Filament\Resources\JournalEntries\Pages\ListJournalEntries;
use App\Models\JournalEntry;
use BackedEnum;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema as DatabaseSchema;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;
    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;
    protected static ?string $navigationLabel = 'Journal';
    protected static ?int $navigationSort = 2;

  
public static function form(Schema $schema): Schema
{
    return $schema->components([
        Section::make('Transaction details')
            ->description('Choose what happened. The system will prepare the accounting entries for you.')
            ->schema([
                Select::make('transaction_type')
                    ->label('What are you recording?')
                    ->options([
                        'expense' => 'Expense paid or owed',
                        'income' => 'Income received',
                        'asset_purchase' => 'Purchase an asset',
                        'loan_received' => 'Loan received',
                        'equity_contribution' => 'Equity or fund contribution',
                        'liability_payment' => 'Pay a liability',
                        'transfer' => 'Transfer between accounts',
                        'adjustment' => 'Other journal adjustment',
                    ])
                    ->default('expense')
                    ->live()
                    ->required()
                    ->native(false)
                    ->dehydrated(),

                DatePicker::make('entry_date')
                    ->label('Transaction date')
                    ->required()
                    ->default(now())
                    ->native(false),

                TextInput::make('reference')
                    ->label('Reference number')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'JRN-' . now()->format('Ymd-His')),

                TextInput::make('description')
                    ->label('What was this transaction for?')
                    ->placeholder('e.g. Electricity bill')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('amount')
                    ->label('Amount')
                    ->prefix('₦')
                    ->numeric()
                    ->minValue(0.01)
                    ->required()
                    ->visible(fn (Get $get) =>
                        $get('transaction_type') !== 'adjustment'
                    ),

                Select::make('payment_status')
                    ->label('Payment status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid / still owed',
                    ])
                    ->default('paid')
                    ->live()
                    ->required()
                    ->native(false)
                    ->visible(fn (Get $get) =>
                        in_array($get('transaction_type'), [
                            'expense',
                            'asset_purchase',
                        ], true)
                    ),
            ])
            ->columns(2),

        Section::make('Choose the accounts')
            ->description('Select the account involved in the transaction. The system determines which side of the journal it belongs on.')
            ->schema([
                Select::make('account_id')
                    ->label('Main account')
                    ->options(function (Get $get): array {
                        $types = match ($get('transaction_type')) {
                            'expense' => ['expense'],
                            'income' => ['income'],
                            'asset_purchase' => ['asset'],
                            'loan_received',
                            'liability_payment' => ['liability'],
                            'equity_contribution' => ['equity'],
                            'transfer' => ['asset'],
                            default => [],
                        };

                        return \App\Models\LedgerAccount::query()
                            ->where('is_active', true)
                            ->whereIn('type', $types)
                            ->orderBy('code')
                            ->get()
                            ->mapWithKeys(fn ($account) => [
                                $account->id => $account->code . ' — ' . $account->name,
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    ->visible(fn (Get $get) =>
                        $get('transaction_type') !== 'adjustment'
                    )
                    ->helperText('For example, choose Operating expenses for an electricity bill.'),

                Select::make('cash_account_id')
                    ->label('Other account')
                    ->options(function (Get $get): array {
                        $type = match ($get('transaction_type')) {
                            'expense', 'asset_purchase' =>
                                $get('payment_status') === 'unpaid'
                                    ? 'liability'
                                    : 'asset',

                            'income',
                            'loan_received',
                            'equity_contribution',
                            'liability_payment',
                            'transfer' => 'asset',

                            default => null,
                        };

                        if (! $type) {
                            return [];
                        }

                        return \App\Models\LedgerAccount::query()
                            ->where('is_active', true)
                            ->where('type', $type)
                            ->orderBy('code')
                            ->get()
                            ->mapWithKeys(fn ($account) => [
                                $account->id => $account->code . ' — ' . $account->name,
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->required()
                    ->visible(fn (Get $get) =>
                        $get('transaction_type') !== 'adjustment'
                    )
                    ->helperText('For unpaid expenses or asset purchases, select a liability account. Otherwise select the cash or bank asset account.'),

                Repeater::make('lines')
                    ->relationship()
                    ->label('Journal lines')
                    ->schema([
                        Select::make('ledger_account_id')
                            ->label('Ledger account')
                            ->relationship('account', 'name')
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => $record->code . ' — ' . $record->name
                            )
                            ->searchable(['code', 'name'])
                            ->preload()
                            ->required(),

                        TextInput::make('debit')
                            ->label('Debit')
                            ->numeric()
                            ->prefix('₦')
                            ->default(0)
                            ->required()
                            ->minValue(0),

                        TextInput::make('credit')
                            ->label('Credit')
                            ->numeric()
                            ->prefix('₦')
                            ->default(0)
                            ->required()
                            ->minValue(0),

                        TextInput::make('memo')
                            ->label('Line description')
                            ->maxLength(255),
                    ])
                    ->columns(4)
                    ->minItems(2)
                    ->defaultItems(2)
                    ->addActionLabel('Add journal line')
                    ->columnSpanFull()
                    ->visible(fn (Get $get) =>
                        $get('transaction_type') === 'adjustment'
                    ),
            ])
            ->columns(2),
    ]);
}


    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('entry_date')->label('Date')->date()->sortable(),
            TextColumn::make('reference')->searchable()->copyable()->weight('bold'),
            TextColumn::make('description')->searchable()->wrap()->limit(70),
            TextColumn::make('debit_total')
            ->label('Amount')
            ->money('NGN')
            ->alignEnd(),
            TextColumn::make('posted_at')->label('Status')->state(fn (JournalEntry $record) => $record->posted_at ? 'Posted' : 'Draft')->badge()->color(fn (string $state) => $state === 'Posted' ? 'success' : 'warning'),
        ])->filters([TernaryFilter::make('posted_at')->label('Posting status')->nullable()->queries(
            true: fn ($query) => $query->whereNotNull('posted_at'),
            false: fn ($query) => $query->whereNull('posted_at'),
            blank: fn ($query) => $query,
        )])->recordActions([EditAction::make()])->defaultSort('entry_date', 'desc')->striped()->paginated([15, 30, 50]);
    }

    public static function getPages(): array
    {
        return ['index' => ListJournalEntries::route('/'), 'create' => CreateJournalEntry::route('/create'), 'edit' => EditJournalEntry::route('/{record}/edit')];
    }

    public static function canViewAny(): bool
    {
        return (auth()->user()?->hasRole('super_admin') ?? false)
            && DatabaseSchema::hasTable('journal_entries');
    }
}
