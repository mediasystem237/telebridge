<?php

namespace Mbindi\Telebridge\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    use HasFactory;

    protected $table = 'telegram_bots';

    protected $fillable = [
        'user_id',
        'license_id',
        'token',
        'name',
        'webhook_url',
        'is_active',
        'integration_data',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'integration_data' => 'array',
    ];

    /**
     * Relation avec le modèle User
     */
    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    /**
     * Relation avec le modèle License
     */
    public function license()
    {
        // Vérifier si le modèle License existe
        if (class_exists('\App\Models\License')) {
            return $this->belongsTo('\App\Models\License', 'license_id');
        }
        return null;
    }

    /**
     * Relation avec les messages Telegram
     */
    public function messages()
    {
        return $this->hasMany(TelegramMessage::class, 'bot_id');
    }

    /**
     * Vérifie si le bot a une licence active
     */
    public function hasActiveLicense(): bool
    {
        if (!$this->user) {
            return false;
        }

        // Vérifier si la méthode activeLicense existe sur User
        if (method_exists($this->user, 'activeLicense')) {
            return $this->user->activeLicense() !== null;
        }

        // Fallback : vérifier directement la relation license
        if ($this->license_id && $this->license) {
            return $this->license->status === 'active';
        }

        return false;
    }

    /**
     * Récupère le nombre de messages restants
     */
    public function getRemainingMessages(): int
    {
        if (!$this->user) {
            return 0;
        }

        if (method_exists($this->user, 'activeLicense')) {
            $license = $this->user->activeLicense();
            return $license?->messages_remaining ?? 0;
        }

        if ($this->license) {
            return $this->license->messages_remaining ?? 0;
        }

        return 0;
    }

    /**
     * Récupère l'URL du webhook pour ce bot
     */
    public function getWebhookUrl(): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/telebridge/webhook/{$this->token}";
    }

    /**
     * Vérifie si le bot est actif
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Active le bot
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Désactive le bot
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
