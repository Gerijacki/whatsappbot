<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Events\MessageQueued;
use App\Filament\Resources\MessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMessage extends EditRecord
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        event(new MessageQueued($this->record));
    }
}
