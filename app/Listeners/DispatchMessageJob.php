<?php

namespace App\Listeners;

use App\Events\MessageQueued;
use App\Jobs\ProcessMessageJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DispatchMessageJob
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageQueued $event)
    {
        ProcessMessageJob::dispatch($event->message);
    }
}
