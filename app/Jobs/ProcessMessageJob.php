<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Throwable;

class ProcessMessageJob implements ShouldQueue
{
    use Dispatchable;

    public $tries = 3;

    public $backoff = 15;

    public function __construct(public Message $message) {}

    public function handle(WhatsAppService $service)
    {
        $this->message->increment('attempts');

        $client = $this->message->client;

        $response = $service->send(
            $this->message->type,
            $client,
            $this->message
        );

        $this->message->update([
            'status' => $response->successful() ? 'sent' : 'failed',
            'response' => $response->body(),
        ]);
    }

    public function failed(Throwable $e)
    {
        $this->message->update([
            'status' => 'failed',
            'response' => $e->getMessage(),
        ]);
    }
}
