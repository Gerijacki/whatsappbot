<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Models\Message;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('whatsapp_client_id')
                ->relationship('client', 'name')
                ->searchable()
                ->required(),

            Select::make('user_id')
                ->relationship('user', 'email')
                ->searchable()
                ->required(),

            Select::make('type')->options([
                'text' => 'Text',
                'template' => 'Template',
                'interactive' => 'Interactive',
            ])->required(),

            TextInput::make('phone_number')->required(),

            TextInput::make('text')->visible(fn ($get) => $get('type') === 'text'),

            TextInput::make('template_name')->visible(fn ($get) => $get('type') === 'template'),
            TextInput::make('language_code')->visible(fn ($get) => $get('type') === 'template'),
            KeyValue::make('parameters')->visible(fn ($get) => $get('type') === 'template'),

            KeyValue::make('interactive_content')->visible(fn ($get) => $get('type') === 'interactive'),

            Select::make('status')->options([
                'pending' => 'Pending',
                'sent' => 'Sent',
                'failed' => 'Failed',
                'retrying' => 'Retrying',
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('type'),
            TextColumn::make('phone_number'),
            TextColumn::make('status')->badge()->colors([
                'success' => 'sent',
                'danger' => 'failed',
                'warning' => 'pending',
            ]),
            TextColumn::make('attempts'),
            TextColumn::make('created_at')->dateTime(),
        ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }
}
