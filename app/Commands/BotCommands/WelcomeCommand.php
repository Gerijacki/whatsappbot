<?php

namespace App\Commands\BotCommands;

use App\Contracts\BotCommandInterface;

class WelcomeCommand implements BotCommandInterface
{
    public function execute(array $webhookData, array $commandParameters = []): array
    {
        $userName = $webhookData['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'] ?? 'Usuario';
        
        $welcomeText = "¡Hola *{$userName}*! 👋\n\n";
        $welcomeText .= "Bienvenido a nuestro bot de WhatsApp. Estoy aquí para ayudarte.\n\n";
        $welcomeText .= "📝 Escribe *ayuda* para ver todos los comandos disponibles.\n";
        $welcomeText .= "❓ Escribe *info* para obtener información sobre nuestros servicios.\n\n";
        $welcomeText .= "¡Espero poder ayudarte! 😊";

        return [
            'type' => 'text',
            'text' => $welcomeText,
        ];
    }

    public function getDescription(): string
    {
        return 'Mensaje de bienvenida para nuevos usuarios';
    }

    public function canHandle(string $messageType): bool
    {
        return $messageType === 'text';
    }
} 