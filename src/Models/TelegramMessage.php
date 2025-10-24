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
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function bot()
    {
        return $this->belongsTo(TelegramBot::class, 'bot_id');
    }

    public function user()
    {
        return $this->belongsTo(TelegramUser::class, 'user_id', 'telegram_id');
    }
}
