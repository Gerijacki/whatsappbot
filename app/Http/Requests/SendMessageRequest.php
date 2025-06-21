<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\WhatsAppClient;

class SendMessageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'whatsapp_client_id' => 'required|exists:whatsapp_clients,id',
            'type' => 'required|in:text,template,interactive',
            'phone_number' => 'required|string',
            'text' => 'nullable|string',
            'template_name' => 'nullable|string',
            'language_code' => 'nullable|string',
            'parameters' => 'nullable|array',
            'interactive_content' => 'nullable|array'
        ];
    }
}