<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BotCommandResource\Pages;
use App\Models\BotCommand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BotCommandResource extends Resource
{
    protected static ?string $model = BotCommand::class;

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?string $navigationGroup = 'Bot Management';

    protected static ?string $navigationLabel = 'Comandos del Bot';

    protected static ?string $modelLabel = 'Comando del Bot';

    protected static ?string $pluralModelLabel = 'Comandos del Bot';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Comando')
                    ->schema([
                        Forms\Components\TextInput::make('trigger_text')
                            ->label('Texto Activador')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Texto que activará este comando. Puede ser una palabra clave o frase.')
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('class_name')
                            ->label('Nombre de la Clase')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nombre completo de la clase que implementa BotCommandInterface (ej: App\\Commands\\BotCommands\\HelpCommand)'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->helperText('Descripción del comando que aparecerá en la ayuda.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->helperText('Si el comando está disponible para los usuarios.'),

                        Forms\Components\TextInput::make('priority')
                            ->label('Prioridad')
                            ->numeric()
                            ->default(0)
                            ->helperText('Prioridad para matching (mayor = más específico). Los comandos con mayor prioridad se ejecutarán primero.'),

                        Forms\Components\KeyValue::make('parameters')
                            ->label('Parámetros Adicionales')
                            ->keyLabel('Clave')
                            ->valueLabel('Valor')
                            ->helperText('Parámetros adicionales que se pasarán al comando (opcional).')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trigger_text')
                    ->label('Texto Activador')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('class_name')
                    ->label('Clase')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->class_name),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('priority', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBotCommands::route('/'),
            'create' => Pages\CreateBotCommand::route('/create'),
            'edit' => Pages\EditBotCommand::route('/{record}/edit'),
        ];
    }
} 