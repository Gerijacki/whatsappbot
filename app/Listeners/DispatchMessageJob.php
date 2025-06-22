<?php

namespace App\Listeners;

use App\Events\MessageQueued;
use App\Jobs\ProcessMessageJob;

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
