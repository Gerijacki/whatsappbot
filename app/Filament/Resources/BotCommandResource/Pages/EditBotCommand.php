<?php

namespace App\Filament\Resources\BotCommandResource\Pages;

use App\Filament\Resources\BotCommandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBotCommand extends EditRecord
{
    protected static string $resource = BotCommandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar Comando'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 