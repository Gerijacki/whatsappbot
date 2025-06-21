<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'whatsapp_client_id', 'type', 'phone_number',
        'template_name', 'language_code', 'parameters',
        'interactive_content', 'text', 'status', 'attempts', 'response'
    ];

    protected $casts = [
        'parameters' => 'array',
        'interactive_content' => 'array',
    ];

    public function client() {
        return $this->belongsTo(WhatsAppClient::class, 'whatsapp_client_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
