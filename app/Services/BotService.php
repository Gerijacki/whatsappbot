<?php

namespace App\Services;

use App\Models\BotCommand;
use App\Models\WhatsAppClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BotService
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Procesar el webhook de WhatsApp
     */
    public function processWebhook(array $webhookData, WhatsAppClient $client): array
    {
        try {
            $validator = Validator::make($webhookData, [
                'entry.0.changes.0.value.messages.0' => 'required|array',
                'entry.0.changes.0.value.contacts.0' => 'required|array',
            ]);

            if ($validator->fails()) {
                Log::error('Webhook validation failed', $validator->errors()->toArray());
                return ['error' => 'Invalid webhook structure'];
            }

            $message = $webhookData['entry'][0]['changes'][0]['value']['messages'][0];
            $contact = $webhookData['entry'][0]['changes'][0]['value']['contacts'][0];
            
            $messageType = $message['type'] ?? 'text';
            $phoneNumber = $contact['wa_id'] ?? null;
            
            if (!$phoneNumber) {
                Log::error('No phone number found in webhook');
                return ['error' => 'No phone number found'];
            }

            return match ($messageType) {
                'text' => $this->processTextMessage($message, $contact, $client),
                'interactive' => $this->processInteractiveMessage($message, $contact, $client),
                'image' => $this->processImageMessage($message, $contact, $client),
                'document' => $this->processDocumentMessage($message, $contact, $client),
                'audio' => $this->processAudioMessage($message, $contact, $client),
                'video' => $this->processVideoMessage($message, $contact, $client),
                'location' => $this->processLocationMessage($message, $contact, $client),
                'contact' => $this->processContactMessage($message, $contact, $client),
                'sticker' => $this->processStickerMessage($message, $contact, $client),
                default => $this->processUnknownMessage($message, $contact, $client),
            };

        } catch (\Exception $e) {
            Log::error('Error processing webhook', [
                'error' => $e->getMessage(),
                'webhook_data' => $webhookData
            ]);
            
            return [
                'type' => 'text',
                'text' => 'Lo siento, ha ocurrido un error procesando tu mensaje. Por favor, intenta de nuevo.'
            ];
        }
    }

    /**
     * Procesar mensaje de texto
     */
    protected function processTextMessage(array $message, array $contact, WhatsAppClient $client): array
    {
        $text = $message['text']['body'] ?? '';
        $phoneNumber = $contact['wa_id'];
        
        Log::info('Processing text message', [
            'phone' => $phoneNumber,
            'text' => $text
        ]);

        $command = BotCommand::findCommand($text);
        
        if (!$command) {
            return [
                'type' => 'text',
                'text' => "No entiendo ese comando. Escribe *ayuda* para ver los comandos disponibles."
            ];
        }

        return $this->executeCommand($command, $message, $contact, $client);
    }

    /**
     * Procesar mensaje interactivo (botones, listas)
     */
    protected function processInteractiveMessage(array $message, array $contact, WhatsAppClient $client): array
    {
        $interactive = $message['interactive'];
        $phoneNumber = $contact['wa_id'];
        
        Log::info('Processing interactive message', [
            'phone' => $phoneNumber,
            'interactive' => $interactive
        ]);

        $interactiveType = $interactive['type'] ?? '';
        
        if ($interactiveType === 'button_reply') {
            $buttonId = $interactive['button_reply']['id'] ?? '';
            return $this->processButtonReply($buttonId, $contact, $client);
        }
        
        if ($interactiveType === 'list_reply') {
            $listId = $interactive['list_reply']['id'] ?? '';
            return $this->processListReply($listId, $contact, $client);
        }

        return [
            'type' => 'text',
            'text' => 'Tipo de mensaje interactivo no soportado.'
        ];
    }

    /**
     * Procesar respuesta de botón
     */
    protected function processButtonReply(string $buttonId, array $contact, WhatsAppClient $client): array
    {
        $command = BotCommand::where('trigger_text', $buttonId)
            ->where('is_active', true)
            ->first();

        if (!$command) {
            return [
                'type' => 'text',
                'text' => 'Opción no válida. Por favor, selecciona una opción de la lista.'
            ];
        }

        $webhookData = [
            'entry' => [
                [
                    'changes' => [
                        [
                            'value' => [
                                'contacts' => [$contact],
                                'messages' => [
                                    [
                                        'type' => 'interactive',
                                        'interactive' => [
                                            'type' => 'button_reply',
                                            'button_reply' => ['id' => $buttonId]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $this->executeCommand($command, $webhookData['entry'][0]['changes'][0]['value']['messages'][0], $contact, $client);
    }

    /**
     * Procesar respuesta de lista
     */
    protected function processListReply(string $listId, array $contact, WhatsAppClient $client): array
    {
        return $this->processButtonReply($listId, $contact, $client);
    }

    /**
     * Ejecutar comando específico
     */
    protected function executeCommand(BotCommand $command, array $message, array $contact, WhatsAppClient $client): array
    {
        try {
            $className = $command->class_name;
            
            if (!class_exists($className)) {
                Log::error("Command class not found: {$className}");
                return [
                    'type' => 'text',
                    'text' => 'Error interno: comando no encontrado.'
                ];
            }

            $commandInstance = new $className();
            
            if (!($commandInstance instanceof \App\Contracts\BotCommandInterface)) {
                Log::error("Command class does not implement BotCommandInterface: {$className}");
                return [
                    'type' => 'text',
                    'text' => 'Error interno: comando mal configurado.'
                ];
            }

            $messageType = $message['type'] ?? 'text';
            if (!$commandInstance->canHandle($messageType)) {
                return [
                    'type' => 'text',
                    'text' => 'Este comando no puede procesar este tipo de mensaje.'
                ];
            }

            $webhookData = [
                'entry' => [
                    [
                        'changes' => [
                            [
                                'value' => [
                                    'contacts' => [$contact],
                                    'messages' => [$message]
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $result = $commandInstance->execute($webhookData, $command->parameters ?? []);

            Log::info('Command executed successfully', [
                'command' => $command->trigger_text,
                'class' => $className,
                'result_type' => $result['type'] ?? 'unknown'
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Error executing command', [
                'command' => $command->trigger_text,
                'class' => $command->class_name,
                'error' => $e->getMessage()
            ]);

            return [
                'type' => 'text',
                'text' => 'Error ejecutando el comando. Por favor, intenta de nuevo.'
            ];
        }
    }

    /**
     * Procesar otros tipos de mensajes
     */
    protected function processImageMessage(array $message, array $contact, WhatsAppClient $client): array
    {
        return [
            'type' => 'text',
            'text' => '📸 He recibido tu imagen. Por favor, escribe un comando de texto para interactuar conmigo.'
        ];
    }

    protected function processDocumentMessage(array $message, array $contact, WhatsAppClient $client): array
    {
        return [
            'type' => 'text',
            'text' => '📄 He recibido tu documento. Por favor, escribe un comando de texto para interactuar conmigo.'
        ];
    }

    protected function processAudioMessage(array $message, array $contact, WhatsAppClient $client): array
    {
        return [
            'type' => 'text',
            'text' => '🎵 He recibido tu audio. Por favor, escribe un comando de texto para interactuar conmigo.'
        ];
    }

    protected function processVideoMessage(array $message, array $contact, WhatsAppClient $client): array
    {
        return [
            'type' => 'text',
            'text' => '🎥 He recibido tu video. Por favor, escribe un comando de texto para interactuar conmigo.'
        ];
    }

    protected function processLocationMessage(array $message, array $contact, WhatsAppClient $client): array
    {
        return [
            'type' => 'text',
            'text' => '📍 He recibido tu ubicación. Por favor, escribe un comando de texto para interactuar conmigo.'
        ];
    }

    protected function processContactMessage(array $message, array $contact, WhatsAppClient $client): array
    {
        return [
            'type' => 'text',
            'text' => '👤 He recibido tu contacto. Por favor, escribe un comando de texto para interactuar conmigo.'
        ];
    }

    protected function processStickerMessage(array $message, array $contact, WhatsAppClient $client): array
    {
        return [
            'type' => 'text',
            'text' => '😊 He recibido tu sticker. Por favor, escribe un comando de texto para interactuar conmigo.'
        ];
    }

    protected function processUnknownMessage(array $message, array $contact, WhatsAppClient $client): array
    {
        return [
            'type' => 'text',
            'text' => '❓ Tipo de mensaje no reconocido. Por favor, escribe un comando de texto para interactuar conmigo.'
        ];
    }
} 