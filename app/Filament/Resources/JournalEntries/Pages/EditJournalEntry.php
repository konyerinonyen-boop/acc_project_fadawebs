<?php

namespace App\Filament\Resources\JournalEntries\Pages;

use App\Filament\Resources\JournalEntries\JournalEntryResource;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditJournalEntry extends EditRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('post')->label('Post entry')->icon('heroicon-o-check-circle')->color('success')->visible(fn (JournalEntry $record) => $record->posted_at === null)
                ->requiresConfirmation()->action(function (JournalEntry $record): void {
                    app(AccountingService::class)->post($record);
                    Notification::make()->title('Journal entry posted')->success()->send();
                    $this->redirect(JournalEntryResource::getUrl('index'));
                }),
            DeleteAction::make()->visible(fn (JournalEntry $record) => $record->posted_at === null),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->posted_at) {
            abort(403, 'Posted journal entries are locked.');
        }

        return $data;
    }
}
