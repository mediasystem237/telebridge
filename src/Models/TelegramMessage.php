<?php

namespace Mbindi\Telebridge\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramMessage extends Model
{
    use HasFactory;

    protected $table = 'telegram_messages';

    protected $fillable = [
        'bot_id',
        'user_id',
        'type',
        'content',
        'response',
        'ai_metadata',
        'intent_data',
        'processed_at',
    ];

    protected $casts = [
        'ai_metadata' => 'array',
        'intent_data' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Relation avec le bot Telegram
     */
    public function bot()
    {
        return $this->belongsTo(TelegramBot::class, 'bot_id');
    }

    /**
     * Relation avec l'utilisateur Telegram
     */
    public function telegramUser()
    {
        return $this->belongsTo(TelegramUser::class, 'user_id', 'telegram_id');
    }

    /**
     * Relation avec le modèle Conversation (Laravel Backend)
     */
    public function conversation()
    {
        // Vérifier si le modèle Conversation existe
        if (class_exists('\App\Models\Conversation')) {
            return $this->belongsTo('\App\Models\Conversation');
        }
        return null;
    }

    /**
     * Vérifie si le message a été traité
     */
    public function isProcessed(): bool
    {
        return $this->processed_at !== null;
    }

    /**
     * Vérifie si le message est un texte
     */
    public function isText(): bool
    {
        return $this->type === 'text';
    }

    /**
     * Vérifie si le message est une photo
     */
    public function isPhoto(): bool
    {
        return $this->type === 'photo';
    }

    /**
     * Vérifie si le message est un document
     */
    public function isDocument(): bool
    {
        return $this->type === 'document';
    }

    /**
     * Vérifie si le message est un audio
     */
    public function isAudio(): bool
    {
        return $this->type === 'audio';
    }

    /**
     * Vérifie si le message est une vidéo
     */
    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    /**
     * Vérifie si le message est une callback query
     */
    public function isCallback(): bool
    {
        return $this->type === 'callback_query';
    }

    /**
     * Récupère le contenu décodé (si JSON)
     */
    public function getDecodedContent()
    {
        if ($this->isText()) {
            return $this->content;
        }

        $decoded = json_decode($this->content, true);
        return $decoded ?? $this->content;
    }

    /**
     * Marque le message comme traité
     */
    public function markAsProcessed(string $response, array $metadata = []): void
    {
        $this->update([
            'response' => $response,
            'ai_metadata' => $metadata,
            'processed_at' => now(),
        ]);
    }

    /**
     * Scope pour les messages non traités
     */
    public function scopeUnprocessed($query)
    {
        return $query->whereNull('processed_at');
    }

    /**
     * Scope pour les messages traités
     */
    public function scopeProcessed($query)
    {
        return $query->whereNotNull('processed_at');
    }

    /**
     * Scope pour un type de message spécifique
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
