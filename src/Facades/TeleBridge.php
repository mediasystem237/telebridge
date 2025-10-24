<?php

namespace Mbindi\Telebridge\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array|null sendMessage(string $token, int $chatId, string $text, array $options = [])
 * @method static array|null sendPhoto(string $token, int $chatId, string $photo, array $options = [])
 * @method static array|null sendDocument(string $token, int $chatId, string $document, array $options = [])
 * @method static array|null sendVideo(string $token, int $chatId, string $video, array $options = [])
 * @method static array|null answerCallbackQuery(string $token, string $callbackQueryId, array $options = [])
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
