<?php

namespace App\Filament\Resources\BotCommandResource\Pages;

use App\Filament\Resources\BotCommandResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBotCommand extends CreateRecord
{
    protected static string $resource = BotCommandResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 