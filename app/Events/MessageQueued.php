<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageQueued
{
    use Dispatchable, SerializesModels;

    public function __construct(public Message $message) {}
}