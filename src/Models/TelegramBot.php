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

    public function owner()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(TelegramMessage::class, 'bot_id');
    }
}
