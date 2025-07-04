<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppClient;
use App\Services\BotService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    protected BotService $botService;
    protected WhatsAppService $whatsAppService;

    public function __construct(BotService $botService, WhatsAppService $whatsAppService)
    {
        $this->botService = $botService;
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Verificar webhook de WhatsApp (GET)
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = config('services.whatsapp.verify_token', 'tu_token_de_verificacion');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('Webhook verified successfully');
            return response($challenge, 200);
        }

        Log::error('Webhook verification failed', [
            'mode' => $mode,
            'token' => $token,
            'expected_token' => $verifyToken
        ]);

        return response('Forbidden', 403);
    }

    /**
     * Recibir webhook de WhatsApp (POST)
     */
    public function receive(Request $request)
    {
        try {
            $payload = $request->all();
            
            Log::info('Webhook received', [
                'payload' => $payload
            ]);

            $validator = Validator::make($payload, [
                'entry' => 'required|array',
                'entry.0.changes' => 'required|array',
                'entry.0.changes.0.value' => 'required|array',
            ]);

            if ($validator->fails()) {
                Log::error('Invalid webhook structure', $validator->errors()->toArray());
                return response()->json(['error' => 'Invalid webhook structure'], 400);
            }

            $value = $payload['entry'][0]['changes'][0]['value'];
            
            if (!isset($value['messages']) || empty($value['messages'])) {
                Log::info('No messages in webhook, might be status update');
                return response()->json(['status' => 'ok']);
            }

            $client = WhatsAppClient::where('is_active', true)->first();
            
            if (!$client) {
                Log::error('No active WhatsApp client found');
                return response()->json(['error' => 'No active client'], 500);
            }

            foreach ($value['messages'] as $message) {
                $this->processMessage($message, $value['contacts'][0] ?? [], $client);
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Error processing webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Procesar un mensaje individual
     */
    protected function processMessage(array $message, array $contact, WhatsAppClient $client)
    {
        try {
            $response = $this->botService->processWebhook([
                'entry' => [
                    [
                        'changes' => [
                            [
                                'value' => [
                                    'messages' => [$message],
                                    'contacts' => [$contact]
                                ]
                            ]
                        ]
                    ]
                ]
            ], $client);

            if (isset($response['error'])) {
                Log::error('Bot processing error', $response);
                return;
            }

            $this->sendResponse($response, $contact['wa_id'], $client);

        } catch (\Exception $e) {
            Log::error('Error processing individual message', [
                'error' => $e->getMessage(),
                'message' => $message,
                'contact' => $contact
            ]);
        }
    }

    /**
     * Enviar respuesta al usuario
     */
    protected function sendResponse(array $response, string $phoneNumber, WhatsAppClient $client)
    {
        try {
            $message = \App\Models\Message::create([
                'user_id' => 1,
                'whatsapp_client_id' => $client->id,
                'type' => $response['type'],
                'phone_number' => $phoneNumber,
                'text' => $response['text'] ?? null,
                'interactive_content' => $response['interactive'] ?? null,
                'status' => 'pending',
            ]);

            $apiResponse = $this->whatsAppService->send(
                $response['type'],
                $client,
                $message
            );

            if ($apiResponse->successful()) {
                $message->update([
                    'status' => 'sent',
                    'response' => $apiResponse->json()
                ]);
                
                Log::info('Message sent successfully', [
                    'phone' => $phoneNumber,
                    'type' => $response['type'],
                    'message_id' => $message->id
                ]);
            } else {
                $message->update([
                    'status' => 'failed',
                    'response' => $apiResponse->json()
                ]);
                
                Log::error('Failed to send message', [
                    'phone' => $phoneNumber,
                    'type' => $response['type'],
                    'error' => $apiResponse->json(),
                    'message_id' => $message->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error sending response', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber,
                'response' => $response
            ]);
        }
    }
} 