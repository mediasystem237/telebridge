<?php

namespace Mbindi\Telebridge\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array|null sendMessage(string $token, int $chatId, string $text)
 * @method static array|null setWebhook(string $token, string $url)
 *
 * @see \Mbindi\Telebridge\Services\TelegramClient
 */
class TeleBridge extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'telebridge';
    }
}
