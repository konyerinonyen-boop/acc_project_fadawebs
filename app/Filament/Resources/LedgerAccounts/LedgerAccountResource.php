<?php

namespace App\Filament\Resources\LedgerAccounts;

use App\Filament\Resources\LedgerAccounts\Pages\CreateLedgerAccount;
use App\Filament\Resources\LedgerAccounts\Pages\EditLedgerAccount;
use App\Filament\Resources\LedgerAccounts\Pages\ListLedgerAccounts;
use App\Models\LedgerAccount;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema as DatabaseSchema;

class LedgerAccountResource extends Resource
{
    protected static ?string $model = LedgerAccount::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Chart of accounts';

    protected static ?string $modelLabel = 'ledger account';

    protected static ?string $pluralModelLabel = 'chart of accounts';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account information')
                ->description('Create a ledger account to organize your organisation’s financial transactions.')
                ->icon(Heroicon::OutlinedIdentification)
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('code')
                            ->label('Account code')
                            ->placeholder('e.g. 1000')
                            ->helperText('Use a unique code to identify this account.')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->prefixIcon(Heroicon::OutlinedHashtag)
                            ->autocomplete(false),

                        TextInput::make('name')
                            ->label('Account name')
                            ->placeholder('e.g. Cash and bank')
                            ->helperText('Choose a clear name your team will recognise.')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon(Heroicon::OutlinedTag)
                            ->autocomplete(false),
                    ]),

                    Select::make('type')
                        ->label('Account category')
                        ->placeholder('Choose an account category')
                        ->options([
                            'asset' => 'Asset — what the organisation owns',
                            'liability' => 'Liability — what the organisation owes',
                            'equity' => 'Equity — accumulated funds or ownership',
                            'income' => 'Income — money earned or received',
                            'expense' => 'Expense — costs of running the organisation',
                        ])
                        ->required()
                        ->native(false)
                        ->searchable()
                        ->prefixIcon(Heroicon::OutlinedFolder)
                        ->helperText('This category determines how the account is classified in financial reports.')
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Description')
                        ->placeholder('Explain what this account is used for (optional).')
                        ->rows(3)
                        ->maxLength(1000)
                        ->columnSpanFull(),
                ])
                ->columns(1),

            Section::make('Account status')
                ->description('Control whether this account can be used for new transactions.')
                ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active account')
                        ->helperText('Inactive accounts should not be used for new journal entries.')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('gray')
                        ->inline(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('name')
                    ->label('Account name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (LedgerAccount $record): ?string =>
                        filled($record->description) ? $record->description : null
                    )
                    ->wrap()
                    ->weight('medium'),

                TextColumn::make('type')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'asset' => 'Asset',
                        'liability' => 'Liability',
                        'equity' => 'Equity',
                        'income' => 'Income',
                        'expense' => 'Expense',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'asset' => 'info',
                        'liability' => 'warning',
                        'equity' => 'gray',
                        'income' => 'success',
                        'expense' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'asset' => Heroicon::OutlinedWallet,
                        'liability' => Heroicon::OutlinedScale,
                        'equity' => Heroicon::OutlinedBuildingLibrary,
                        'income' => Heroicon::OutlinedArrowTrendingUp,
                        'expense' => Heroicon::OutlinedArrowTrendingDown,
                        default => Heroicon::OutlinedFolder,
                    })
                    ->sortable(),

                TextColumn::make('lines_count')
                    ->counts('lines')
                    ->label('Journal activity')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Account category')
                    ->options([
                        'asset' => 'Asset',
                        'liability' => 'Liability',
                        'equity' => 'Equity',
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Account status')
                    ->placeholder('All accounts')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Manage')
                    ->icon(Heroicon::OutlinedPencilSquare),
            ])
            ->defaultSort('code')
            ->striped()
            ->paginated([15, 30, 50])
            ->defaultPaginationPageOption(15);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLedgerAccounts::route('/'),
            'create' => CreateLedgerAccount::route('/create'),
            'edit' => EditLedgerAccount::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return (auth()->user()?->hasRole('super_admin') ?? false)
            && DatabaseSchema::hasTable('ledger_accounts');
    }
}