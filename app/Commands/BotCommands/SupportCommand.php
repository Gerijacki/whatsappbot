<?php

namespace App\Commands\BotCommands;

use App\Contracts\BotCommandInterface;

class SupportCommand implements BotCommandInterface
{
    public function execute(array $webhookData, array $commandParameters = []): array
    {
        return [
            'type' => 'text',
            'text' => "🛠️ *Soporte Técnico 24/7*\n\n" .
                      "Nuestro equipo de soporte técnico está disponible las 24 horas del día, 7 días a la semana para ayudarte con cualquier problema.\n\n" .
                      "📞 *Contacto:*\n" .
                      "• Teléfono: +1 (555) 123-4567\n" .
                      "• Email: soporte@empresa.com\n" .
                      "• Chat en vivo: Disponible en nuestro sitio web\n\n" .
                      "⏰ *Horarios de atención:*\n" .
                      "• Lunes a Domingo: 24/7\n" .
                      "• Tiempo de respuesta: < 30 minutos\n\n" .
                      "🔧 *Servicios incluidos:*\n" .
                      "• Diagnóstico de problemas\n" .
                      "• Instalación y configuración\n" .
                      "• Mantenimiento preventivo\n" .
                      "• Actualizaciones de software\n\n" .
                      "¿Necesitas ayuda específica? Escribe *contacto* para hablar con un técnico."
        ];
    }

    public function getDescription(): string
    {
        return 'Información sobre nuestro servicio de soporte técnico';
    }

    public function canHandle(string $messageType): bool
    {
        return $messageType === 'text' || $messageType === 'interactive';
    }
} 