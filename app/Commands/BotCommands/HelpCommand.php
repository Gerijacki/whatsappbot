<?php

namespace App\Commands\BotCommands;

use App\Contracts\BotCommandInterface;
use App\Models\BotCommand;

class HelpCommand implements BotCommandInterface
{
    public function execute(array $webhookData, array $commandParameters = []): array
    {
        $commands = BotCommand::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        $helpText = "🤖 *Comandos disponibles:*\n\n";
        
        foreach ($commands as $command) {
            $helpText .= "• *{$command->trigger_text}* - {$command->description}\n";
        }

        $helpText .= "\n💡 Escribe cualquier comando para obtener ayuda específica.";

        return [
            'type' => 'text',
            'text' => $helpText,
        ];
    }

    public function getDescription(): string
    {
        return 'Muestra todos los comandos disponibles del bot';
    }

    public function canHandle(string $messageType): bool
    {
        return $messageType === 'text';
    }
} 