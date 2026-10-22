<?php

namespace App\Filament\Resources\BusinessTransactions;

use App\Filament\Resources\BusinessTransactions\Pages\CreateBusinessTransaction;
use App\Filament\Resources\BusinessTransactions\Pages\ListBusinessTransactions;
use App\Models\BusinessTransaction;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema as DatabaseSchema;

class BusinessTransactionResource extends Resource
{
    protected static ?string $model = BusinessTransaction::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Business transactions';

    protected static ?string $modelLabel = 'business transaction';

    protected static ?string $pluralModelLabel = 'business transactions';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Record an expense')
                ->description(
                    'Record what the organisation spent, when it happened, and how it was paid.'
                )
                ->icon('heroicon-o-receipt-percent')
                ->columns(2)
                ->schema([
                    Select::make('transaction_type')
                        ->label('Transaction type')
                        ->options([
                            'expense' => 'Expense',
                        ])
                        ->default('expense')
                        ->required()
                        ->native(false)
                        ->disabled(),

                    DatePicker::make('transaction_date')
                        ->label('Transaction date')
                        ->default(now())
                        ->required()
                        ->native(false),

                    TextInput::make('amount')
                        ->label('Amount')
                        ->prefix('₦')
                        ->numeric()
                        ->minValue(0.01)
                        ->step(0.01)
                        ->required(),

                    TextInput::make('description')
                        ->label('What was this expense for?')
                        ->placeholder('e.g. Electricity bill')
                        ->maxLength(255)
                        ->required()
                        ->columnSpanFull(),

                    Select::make('payment_method')
                        ->label('Payment method / account')
                        ->options([
                            'bank' => 'Bank',
                            'cash' => 'Cash',
                        ])
                        ->default('bank')
                        ->required()
                        ->native(false),

                    Select::make('payment_status')
                        ->label('Payment status')
                        ->options([
                            'paid' => 'Paid',
                            'unpaid' => 'Unpaid',
                        ])
                        ->default('paid')
                        ->required()
                        ->native(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->wrap()
                    ->weight('medium'),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('NGN')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Paid through')
                    ->formatStateUsing(
                        fn (string $state): string => ucfirst($state)
                    )
                    ->badge(),

                TextColumn::make('payment_status')
                    ->label('Payment status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('journalEntry.reference')
                    ->label('Journal reference')
                    ->placeholder('—')
                    ->copyable(),

                TextColumn::make('creator.name')
                    ->label('Recorded by')
                    ->placeholder('System'),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->striped()
            ->paginated([15, 30, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBusinessTransactions::route('/'),
            'create' => CreateBusinessTransaction::route('/create'),
        ];
    }

    public static function canViewAny(): bool
    {
        return (auth()->user()?->hasRole('super_admin') ?? false)
            && DatabaseSchema::hasTable('business_transactions')
            && DatabaseSchema::hasTable('journal_entries')
            && DatabaseSchema::hasTable('ledger_accounts');
    }
}
