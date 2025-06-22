<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppClient extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_clients';

    protected $fillable = ['name', 'phone_number_id', 'business_account_id', 'access_token', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
