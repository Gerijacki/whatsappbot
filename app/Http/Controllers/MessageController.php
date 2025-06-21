<?php

namespace App\Http\Controllers;

use App\Events\MessageQueued;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Models\Message;

class MessageController extends Controller
{
    public function store(SendMessageRequest $request)
    {
        $data = $request->validated();

        $message = Message::create([
            ...$data,
            'user_id' => auth()->id() ?? 1,
            'status' => 'pending'
        ]);

        event(new MessageQueued($message));

        return response()->json(['status' => 'queued']);
    }
}
