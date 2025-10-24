<?php

namespace Mbindi\Telebridge\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Mbindi\Telebridge\Services\TelegramClient bot(string $name = 'default')
 * @method static array|null sendMessage(string $token, int $chatId, string $text, array $options = [])
 * @method static array|null sendPhoto(string $token, int $chatId, string $photo, array $options = [])
 * @method static array|null sendDocument(string $token, int $chatId, string $document, array $options = [])
 * @method static array|null sendVideo(string $token, int $chatId, string $video, array $options = [])
 * @method static array|null sendAudio(string $token, int $chatId, string $audio, array $options = [])
 * @method static array|null sendVoice(string $token, int $chatId, string $voice, array $options = [])
 * @method static array|null sendSticker(string $token, int $chatId, string $sticker, array $options = [])
 * @method static array|null sendLocation(string $token, int $chatId, float $latitude, float $longitude, array $options = [])
 * @method static array|null sendContact(string $token, int $chatId, string $phoneNumber, string $firstName, array $options = [])
 * @method static array|null editMessageText(string $token, int $chatId, int $messageId, string $text, array $options = [])
 * @method static array|null deleteMessage(string $token, int $chatId, int $messageId)
 * @method static array|null answerCallbackQuery(string $token, string $callbackQueryId, array $options = [])
 * @method static array|null answerInlineQuery(string $token, string $inlineQueryId, array $results, array $options = [])
 * @method static array|null sendChatAction(string $token, int $chatId, string $action)
 * @method static array|null setWebhook(string $token, string $url, array $options = [])
 * @method static array|null deleteWebhook(string $token, bool $dropPendingUpdates = false)
 * @method static array|null getWebhookInfo(string $token)
 * @method static array|null getMe(string $token)
 * @method static array|null getFile(string $token, string $fileId)
 * @method static array|null getLastError()
 * @method static array|null getLastResponse()
 * @method static bool hasError(string $errorDescription)
 * @method static void resetErrors()
 * @method static \Mbindi\Telebridge\Data\BotInfo|null getBotInfo(string $token)
 * @method static \Mbindi\Telebridge\Data\WebhookInfo|null getWebhookData(string $token)
 *
 * @see \Mbindi\Telebridge\TeleBridgeManager
 */
class TeleBridge extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'telebridge';
    }
}
