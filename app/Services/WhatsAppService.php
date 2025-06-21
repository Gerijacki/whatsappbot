<?php
namespace App\Services;

use App\Models\Message;
use App\Models\WhatsAppClient;
use Illuminate\Support\Facades\Http;

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
                'text' => ['body' => $message->text]
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
                            'parameters' => array_map(fn($p) => ['type' => 'text', 'text' => $p], $message->parameters ?? [])
                        ]
                    ]
                ]
            ],
            'interactive' => [
                ...$basePayload,
                'type' => 'interactive',
                'interactive' => $message->interactive_content
            ],
        };

        return Http::withToken($client->access_token)
            ->post($endpoint, $payload);
    }
}