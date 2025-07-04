<?php

namespace App\Commands\BotCommands;

use App\Contracts\BotCommandInterface;

class DevelopmentCommand implements BotCommandInterface
{
    public function execute(array $webhookData, array $commandParameters = []): array
    {
        return [
            'type' => 'text',
            'text' => "📱 *Desarrollo de Aplicaciones*\n\n" .
                      "Somos expertos en el desarrollo de aplicaciones móviles y web de alta calidad.\n\n" .
                      "🚀 *Tecnologías que manejamos:*\n" .
                      "• **Frontend:** React, Vue.js, Angular, Flutter\n" .
                      "• **Backend:** Laravel, Node.js, Python, Java\n" .
                      "• **Móvil:** iOS, Android, React Native\n" .
                      "• **Base de datos:** MySQL, PostgreSQL, MongoDB\n\n" .
                      "💼 *Tipos de proyectos:*\n" .
                      "• Aplicaciones web empresariales\n" .
                      "• E-commerce y marketplaces\n" .
                      "• Apps móviles nativas e híbridas\n" .
                      "• APIs y microservicios\n" .
                      "• Sistemas de gestión (CRM, ERP)\n\n" .
                      "📋 *Proceso de desarrollo:*\n" .
                      "1. Análisis de requisitos\n" .
                      "2. Diseño de arquitectura\n" .
                      "3. Desarrollo iterativo\n" .
                      "4. Testing y QA\n" .
                      "5. Despliegue y mantenimiento\n\n" .
                      "💰 *Precios desde:* $5,000 USD\n" .
                      "⏱️ *Tiempo estimado:* 4-12 semanas\n\n" .
                      "¿Te interesa? Escribe *cotizar* para obtener una propuesta personalizada."
        ];
    }

    public function getDescription(): string
    {
        return 'Información sobre nuestros servicios de desarrollo';
    }

    public function canHandle(string $messageType): bool
    {
        return $messageType === 'text' || $messageType === 'interactive';
    }
} 