<?php

namespace App\Console\Commands;

use App\Models\BotCommand;
use App\Models\WhatsAppClient;
use App\Services\BotService;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class TestBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:test {message : Mensaje a procesar} {--phone=1234567890 : Número de teléfono} {--whatsappClientId=1 : Id whatsapp client}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el bot con un mensaje específico y enviarlo realmente a WhatsApp';

    /**
     * Execute the console command.
     */
    public function handle(BotService $botService, WhatsAppService $whatsAppService)
    {
        $message = $this->argument('message');
        $phone = $this->option('phone');
        $whatsappClientId = $this->option('whatsappClientId');

        $this->info("🧪 Probando bot con mensaje: '{$message}'");
        $this->info("📱 Número de teléfono: {$phone}");
        $this->info("💬 Enviando mensaje real a WhatsApp...");

        // Obtener cliente de WhatsApp
        $client = WhatsAppClient::where('id', $whatsappClientId)->first();
        
        if (!$client) {
            $this->error('❌ No se encontró un cliente de WhatsApp activo con ese ID');
            return 1;
        }

        // Simular webhook data
        $webhookData = [
            'entry' => [
                [
                    'changes' => [
                        [
                            'value' => [
                                'messages' => [
                                    [
                                        'type' => 'text',
                                        'text' => [
                                            'body' => $message
                                        ]
                                    ]
                                ],
                                'contacts' => [
                                    [
                                        'wa_id' => $phone,
                                        'profile' => [
                                            'name' => 'Usuario de Prueba'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        try {
            // Procesar con el bot
            $response = $botService->processWebhook($webhookData, $client);

            if (isset($response['error'])) {
                $this->error("❌ Error: {$response['error']}");
                return 1;
            }

            $this->info("✅ Respuesta del bot:");
            $this->line("Tipo: {$response['type']}");
            
            if ($response['type'] === 'text') {
                $this->line("Texto: {$response['text']}");
            } elseif ($response['type'] === 'interactive') {
                $this->line("Interactivo: " . json_encode($response['interactive'], JSON_PRETTY_PRINT));
            }

            // Mostrar comando encontrado
            $command = BotCommand::findCommand($message);
            if ($command) {
                $this->info("🎯 Comando encontrado: {$command->trigger_text} -> {$command->class_name}");
            } else {
                $this->warn("⚠️ No se encontró comando para: '{$message}'");
            }

            // Enviar realmente el mensaje a WhatsApp
            $this->info("🚀 Enviando mensaje real a WhatsApp...");
            $sendResult = null;
            if ($response['type'] === 'text') {
                $sendResult = $whatsAppService->sendText($client, $phone, $response['text']);
            } elseif ($response['type'] === 'interactive') {
                $sendResult = $whatsAppService->sendInteractive($client, $phone, $response['interactive']);
            } else {
                $sendResult = $whatsAppService->send($response['type'], $client, new \App\Models\Message([
                    'phone_number' => $phone,
                    'type' => $response['type'],
                    'text' => $response['text'] ?? null,
                    'interactive_content' => $response['interactive'] ?? null,
                ]));
            }

            if ($sendResult && $sendResult->successful()) {
                $this->info('✅ Mensaje enviado correctamente a WhatsApp.');
            } else {
                $this->error('❌ Error enviando el mensaje a WhatsApp.');
                if ($sendResult) {
                    $this->line($sendResult->body());
                }
            }

        } catch (\Exception $e) {
            $this->error("❌ Error procesando mensaje: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
} 