<?php

namespace App\Filament\Resources\JournalEntries;

use App\Filament\Resources\JournalEntries\Pages\CreateJournalEntry;
use App\Filament\Resources\JournalEntries\Pages\EditJournalEntry;
use App\Filament\Resources\JournalEntries\Pages\ListJournalEntries;
use App\Models\JournalEntry;
use BackedEnum;
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
            Section::make('Entry details')->description('Record income, expenses, transfers, and adjustments as balanced debit and credit lines.')
                ->schema([
                    DatePicker::make('entry_date')->required()->default(now())->native(false),
                    TextInput::make('reference')->required()->maxLength(255)->unique(ignoreRecord: true)->default(fn () => 'JRN-' . now()->format('Ymd-His')),
                    TextInput::make('description')->required()->maxLength(255)->columnSpanFull(),
                ]),
                    Section::make('Journal lines')
            ->description('Add the ledger accounts and enter the debit and credit amounts for each line.')
            ->schema([
                Repeater::make('lines')
                    ->relationship()
                    ->schema([
                        Select::make('ledger_account_id')
                            ->label('Account')
                            ->relationship('account', 'name')
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => $record->name
                            )
                            ->searchable(['code', 'name'])
                            ->preload()
                            ->required(),

                        TextInput::make('debit')
                            ->numeric()
                            ->prefix('₦')
                            ->default(0)
                            ->required()
                            ->minValue(0),

                        TextInput::make('credit')
                            ->numeric()
                            ->prefix('₦')
                            ->default(0)
                            ->required()
                            ->minValue(0),

                        TextInput::make('memo')
                            ->maxLength(255),
                    ])
                    ->columns(4)
                    ->minItems(2)
                    ->defaultItems(2)
                    ->addActionLabel('Add journal line')
                    ->columnSpanFull(),
                          ])->columns(2),
                          
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('entry_date')->label('Date')->date()->sortable(),
            TextColumn::make('reference')->searchable()->copyable()->weight('bold'),
            TextColumn::make('description')->searchable()->wrap()->limit(70),
            TextColumn::make('lines_sum_debit')->label('Amount')->sum('lines', 'debit')->money('NGN')->sortable(),
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
