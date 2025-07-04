<?php

namespace App\Services;

use App\Models\Message;
use App\Models\WhatsAppClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function send(string $type, WhatsAppClient $client, Message $message)
    {
        $endpoint = "https://graph.facebook.com/v19.0/{$client->phone_number_id}/messages";

        $basePayload = [
            'messaging_product' => 'whatsapp',
            'to' => $message->phone_number,
        ];

        $payload = match ($type) {
            'text' => [
                ...$basePayload,
                'type' => 'text',
                'text' => ['body' => $message->text],
            ],
            'template' => [
                ...$basePayload,
                'type' => 'template',
                'template' => [
                    'name' => $message->template_name,
                    'language' => ['code' => $message->language_code],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => array_map(fn ($p) => ['type' => 'text', 'text' => $p], $message->parameters ?? []),
                        ],
                    ],
                ],
            ],
            'interactive' => [
                ...$basePayload,
                'type' => 'interactive',
                'interactive' => $message->interactive_content,
            ],
            'image' => [
                ...$basePayload,
                'type' => 'image',
                'image' => [
                    'link' => $message->text, // URL de la imagen
                    'caption' => $message->interactive_content['caption'] ?? null,
                ],
            ],
            'document' => [
                ...$basePayload,
                'type' => 'document',
                'document' => [
                    'link' => $message->text, // URL del documento
                    'caption' => $message->interactive_content['caption'] ?? null,
                    'filename' => $message->interactive_content['filename'] ?? null,
                ],
            ],
            'audio' => [
                ...$basePayload,
                'type' => 'audio',
                'audio' => [
                    'link' => $message->text, // URL del audio
                ],
            ],
            'video' => [
                ...$basePayload,
                'type' => 'video',
                'video' => [
                    'link' => $message->text, // URL del video
                    'caption' => $message->interactive_content['caption'] ?? null,
                ],
            ],
            'location' => [
                ...$basePayload,
                'type' => 'location',
                'location' => $message->interactive_content,
            ],
            'contact' => [
                ...$basePayload,
                'type' => 'contacts',
                'contacts' => $message->interactive_content,
            ],
            'sticker' => [
                ...$basePayload,
                'type' => 'sticker',
                'sticker' => [
                    'link' => $message->text, // URL del sticker
                ],
            ],
            default => [
                ...$basePayload,
                'type' => 'text',
                'text' => ['body' => 'Tipo de mensaje no soportado'],
            ],
        };

        Log::info('Sending WhatsApp message', [
            'type' => $type,
            'phone' => $message->phone_number,
            'payload' => $payload
        ]);

        $response = Http::withToken($client->access_token)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($endpoint, $payload);
        Log::info($response);

        if (!$response->successful()) {
            Log::error('WhatsApp API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload
            ]);
        }

        return $response;
    }

    /**
     * Enviar mensaje de texto simple
     */
    public function sendText(WhatsAppClient $client, string $phoneNumber, string $text)
    {
        $message = new Message([
            'phone_number' => $phoneNumber,
            'text' => $text,
            'type' => 'text',
        ]);

        return $this->send('text', $client, $message);
    }

    /**
     * Enviar mensaje interactivo
     */
    public function sendInteractive(WhatsAppClient $client, string $phoneNumber, array $interactiveContent)
    {
        $message = new Message([
            'phone_number' => $phoneNumber,
            'interactive_content' => $interactiveContent,
            'type' => 'interactive',
        ]);

        return $this->send('interactive', $client, $message);
    }

    /**
     * Enviar botones interactivos
     */
    public function sendButtons(WhatsAppClient $client, string $phoneNumber, string $bodyText, array $buttons)
    {
        $interactiveContent = [
            'type' => 'button',
            'body' => [
                'text' => $bodyText
            ],
            'action' => [
                'buttons' => $buttons
            ]
        ];

        return $this->sendInteractive($client, $phoneNumber, $interactiveContent);
    }

    /**
     * Enviar lista interactiva
     */
    public function sendList(WhatsAppClient $client, string $phoneNumber, string $bodyText, string $buttonText, array $sections)
    {
        $interactiveContent = [
            'type' => 'list',
            'body' => [
                'text' => $bodyText
            ],
            'action' => [
                'button' => $buttonText,
                'sections' => $sections
            ]
        ];

        return $this->sendInteractive($client, $phoneNumber, $interactiveContent);
    }

    /**
     * Enviar imagen
     */
    public function sendImage(WhatsAppClient $client, string $phoneNumber, string $imageUrl, ?string $caption = null)
    {
        $message = new Message([
            'phone_number' => $phoneNumber,
            'text' => $imageUrl,
            'interactive_content' => $caption ? ['caption' => $caption] : null,
            'type' => 'image',
        ]);

        return $this->send('image', $client, $message);
    }

    /**
     * Enviar documento
     */
    public function sendDocument(WhatsAppClient $client, string $phoneNumber, string $documentUrl, ?string $caption = null, ?string $filename = null)
    {
        $message = new Message([
            'phone_number' => $phoneNumber,
            'text' => $documentUrl,
            'interactive_content' => [
                'caption' => $caption,
                'filename' => $filename,
            ],
            'type' => 'document',
        ]);

        return $this->send('document', $client, $message);
    }
}
