<?php

namespace App\Filament\Resources\WhatsAppClientResource\Pages;

use App\Filament\Resources\WhatsAppClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWhatsAppClients extends ListRecords
{
    protected static string $resource = WhatsAppClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
