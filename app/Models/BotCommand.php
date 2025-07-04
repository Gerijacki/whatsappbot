<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotCommand extends Model
{
    use HasFactory;

    protected $fillable = [
        'trigger_text',
        'class_name',
        'description',
        'is_active',
        'priority',
        'parameters',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'parameters' => 'array',
    ];

    /**
     * Buscar el comando más apropiado basado en el texto del mensaje
     */
    public static function findCommand(string $messageText): ?self
    {
        return self::where('is_active', true)
            ->where(function ($query) use ($messageText) {
                $query->where('trigger_text', 'LIKE', "%{$messageText}%")
                      ->orWhereRaw('? LIKE CONCAT("%", trigger_text, "%")', [$messageText]);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * Verificar si un texto coincide exactamente con el trigger
     */
    public function matchesExact(string $text): bool
    {
        return strtolower(trim($text)) === strtolower(trim($this->trigger_text));
    }

    /**
     * Verificar si un texto contiene el trigger
     */
    public function matchesContains(string $text): bool
    {
        return stripos($text, $this->trigger_text) !== false;
    }
} 