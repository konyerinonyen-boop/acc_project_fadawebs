<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\JournalEntries\JournalEntryResource;
use App\Models\JournalEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Schema;

class RecentJournalEntries extends TableWidget
{
    protected static ?string $heading = 'Recent journal activity';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return (auth()->user()?->hasRole('super_admin') ?? false)
            && Schema::hasTable('journal_entries');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(JournalEntry::query()->with('lines')->latest('entry_date')->latest('id'))
            ->columns([
                TextColumn::make('entry_date')->label('Date')->date()->sortable(),
                TextColumn::make('reference')->label('Reference')->searchable()->copyable(),
                TextColumn::make('description')->wrap()->limit(60),
                TextColumn::make('lines_sum_debit')->label('Amount')->state(fn (JournalEntry $record) => $record->lines->sum('debit'))->money('NGN'),
                TextColumn::make('posted_at')->label('Status')->state(fn (JournalEntry $record) => $record->posted_at ? 'Posted' : 'Draft')->badge()->color(fn (string $state) => $state === 'Posted' ? 'success' : 'warning'),
            ])
            ->recordUrl(fn (JournalEntry $record) => JournalEntryResource::getUrl('edit', ['record' => $record]));
    }
}
