<?php

namespace App\Filament\Resources\WhatsAppClientResource\Pages;

use App\Filament\Resources\WhatsAppClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWhatsAppClient extends EditRecord
{
    protected static string $resource = WhatsAppClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
