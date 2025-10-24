<?php

namespace Mbindi\Telebridge\Notifications;

use Illuminate\Notifications\Notification;
use Mbindi\Telebridge\Services\TelegramClient;

class TelegramChannel
{
    protected TelegramClient $client;

    public function __construct(TelegramClient $client)
    {
        $this->client = $client;
    }

    /**
     * Envoie la notification
     */
    public function send($notifiable, Notification $notification)
    {
        // Obtenir le message de la notification
        $message = $notification->toTelegram($notifiable);

        if (!$message) {
            return;
        }

        // Obtenir le chat ID
        $chatId = $this->getChatId($notifiable, $notification);
        if (!$chatId) {
            return;
        }

        // Obtenir le token
        $token = $this->getToken($notifiable, $notification);
        if (!$token) {
            return;
        }

        // Envoyer selon le type de message
        if ($message instanceof TelegramMessage) {
            $this->sendMessage($token, $chatId, $message);
        } elseif (is_string($message)) {
            $this->client->sendMessage($token, $chatId, $message);
        } elseif (is_array($message)) {
            $this->client->sendMessage(
                $token,
                $chatId,
                $message['text'] ?? '',
                $message['options'] ?? []
            );
        }
    }

    /**
     * Envoie un message TelegramMessage
     */
    protected function sendMessage(string $token, int $chatId, TelegramMessage $message): void
    {
        if ($message->photo) {
            $this->client->sendPhoto($token, $chatId, $message->photo, [
                'caption' => $message->text,
                'parse_mode' => $message->parseMode,
                'reply_markup' => $message->keyboard,
            ]);
        } elseif ($message->document) {
            $this->client->sendDocument($token, $chatId, $message->document, [
                'caption' => $message->text,
                'parse_mode' => $message->parseMode,
                'reply_markup' => $message->keyboard,
            ]);
        } else {
            $this->client->sendMessage($token, $chatId, $message->text, [
                'parse_mode' => $message->parseMode,
                'reply_markup' => $message->keyboard,
                'disable_web_page_preview' => $message->disableWebPagePreview,
            ]);
        }
    }

    /**
     * Récupère le chat ID
     */
    protected function getChatId($notifiable, Notification $notification): ?int
    {
        // Méthode 1 : Méthode routeNotificationForTelegram()
        if (method_exists($notifiable, 'routeNotificationForTelegram')) {
            return $notifiable->routeNotificationForTelegram($notification);
        }

        // Méthode 2 : Attribut telegram_chat_id
        if (isset($notifiable->telegram_chat_id)) {
            return (int) $notifiable->telegram_chat_id;
        }

        // Méthode 3 : Relation telegramUser
        if (method_exists($notifiable, 'telegramUser') && $notifiable->telegramUser) {
            return (int) $notifiable->telegramUser->telegram_id;
        }

        return null;
    }

    /**
     * Récupère le token du bot
     */
    protected function getToken($notifiable, Notification $notification): ?string
    {
        // Méthode 1 : Dans la notification
        if (method_exists($notification, 'telegramToken')) {
            return $notification->telegramToken($notifiable);
        }

        // Méthode 2 : Méthode routeNotificationForTelegramToken()
        if (method_exists($notifiable, 'routeNotificationForTelegramToken')) {
            return $notifiable->routeNotificationForTelegramToken($notification);
        }

        // Méthode 3 : Relation telegramBot
        if (method_exists($notifiable, 'telegramBot') && $notifiable->telegramBot) {
            return $notifiable->telegramBot->token;
        }

        // Méthode 4 : Config par défaut
        return config('telebridge.bot.token');
    }
}

