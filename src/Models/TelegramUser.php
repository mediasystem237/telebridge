<?php

namespace Mbindi\Telebridge\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    use HasFactory;

    protected $table = 'telegram_users';

    protected $primaryKey = 'telegram_id';
    public $incrementing = false;

    protected $fillable = [
        'telegram_id',
        'username',
        'first_name',
        'last_name',
        'context',
        'last_seen',
    ];

    protected $casts = [
        'context' => 'array',
        'last_seen' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(TelegramMessage::class, 'user_id', 'telegram_id');
    }
}
