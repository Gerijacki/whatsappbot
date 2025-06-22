<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Events\MessageQueued;
use App\Filament\Resources\MessageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMessage extends CreateRecord
{
    protected static string $resource = MessageResource::class;

    protected function afterCreate(): void
    {
        event(new MessageQueued($this->record));
    }
}
