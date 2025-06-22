<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WhatsAppClientResource\Pages;
use App\Models\WhatsAppClient;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WhatsAppClientResource extends Resource
{
    protected static ?string $model = WhatsAppClient::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('phone_number_id')->required(),
            TextInput::make('business_account_id')->required(),
            TextInput::make('access_token')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('name')->searchable(),
            TextColumn::make('phone_number_id'),
        ])->filters([])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWhatsAppClients::route('/'),
            'create' => Pages\CreateWhatsAppClient::route('/create'),
            'edit' => Pages\EditWhatsAppClient::route('/{record}/edit'),
        ];
    }
}
