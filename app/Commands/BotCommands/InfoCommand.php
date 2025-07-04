<?php

namespace App\Commands\BotCommands;

use App\Contracts\BotCommandInterface;

class InfoCommand implements BotCommandInterface
{
    public function execute(array $webhookData, array $commandParameters = []): array
    {
        return [
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => "📋 *Información de nuestros servicios*\n\n" .
                              "Ofrecemos una amplia gama de servicios para ayudarte:\n\n" .
                              "• 🛠️ Soporte técnico 24/7\n" .
                              "• 📱 Desarrollo de aplicaciones\n" .
                              "• 🌐 Diseño web profesional\n" .
                              "• 📊 Consultoría tecnológica\n\n" .
                              "Selecciona una opción para obtener más información:"
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'support',
                                'title' => '🛠️ Soporte'
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'development',
                                'title' => '📱 Desarrollo'
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'design',
                                'title' => '🌐 Diseño'
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'consulting',
                                'title' => '📊 Consultoría'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getDescription(): string
    {
        return 'Muestra información sobre nuestros servicios con botones interactivos';
    }

    public function canHandle(string $messageType): bool
    {
        return $messageType === 'text';
    }
} 