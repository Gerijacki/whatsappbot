<?php

namespace App\Commands\BotCommands;

use App\Contracts\BotCommandInterface;

class ConsultingCommand implements BotCommandInterface
{
    public function execute(array $webhookData, array $commandParameters = []): array
    {
        return [
            'type' => 'text',
            'text' => "📊 *Consultoría Tecnológica*\n\n" .
                      "Ofrecemos servicios de consultoría especializada para optimizar tu infraestructura tecnológica.\n\n" .
                      "🔍 *Áreas de consultoría:*\n" .
                      "• Arquitectura de software\n" .
                      "• Migración a la nube\n" .
                      "• Optimización de bases de datos\n" .
                      "• Seguridad informática\n" .
                      "• Automatización de procesos\n" .
                      "• Transformación digital\n\n" .
                      "👥 *Nuestro equipo:*\n" .
                      "• Arquitectos de software senior\n" .
                      "• Especialistas en DevOps\n" .
                      "• Expertos en seguridad\n" .
                      "• Consultores de negocio\n" .
                      "• Project Managers certificados\n\n" .
                      "📈 *Beneficios:*\n" .
                      "• Reducción de costos operativos\n" .
                      "• Mejora en la eficiencia\n" .
                      "• Mayor seguridad y escalabilidad\n" .
                      "• ROI medible y documentado\n" .
                      "• Soporte continuo\n\n" .
                      "📋 *Metodología:*\n" .
                      "1. Evaluación inicial\n" .
                      "2. Análisis de necesidades\n" .
                      "3. Propuesta de solución\n" .
                      "4. Implementación guiada\n" .
                      "5. Seguimiento y optimización\n\n" .
                      "💰 *Tarifas:* $150-300 USD/hora\n" .
                      "📅 *Disponibilidad:* Lunes a Viernes, 9AM-6PM\n\n" .
                      "¿Quieres agendar una consulta? Escribe *agendar* para programar una sesión gratuita de 30 minutos."
        ];
    }

    public function getDescription(): string
    {
        return 'Información sobre nuestros servicios de consultoría';
    }

    public function canHandle(string $messageType): bool
    {
        return $messageType === 'text' || $messageType === 'interactive';
    }
} 