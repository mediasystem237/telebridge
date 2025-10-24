<?php

namespace Mbindi\Telebridge\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramClient
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = 'https://api.telegram.org/bot';
    }

    /**
     * Construit l'URL de l'API Telegram
     */
    protected function buildUrl(string $token, string $method): string
    {
        return "{$this->apiBaseUrl}{$token}/{$method}";
    }

    /**
     * Configure le webhook
     */
    public function setWebhook(string $token, string $url, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'setWebhook');
        $params = array_merge(['url' => $url], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Supprime le webhook
     */
    public function deleteWebhook(string $token): ?array
    {
        $apiUrl = $this->buildUrl($token, 'deleteWebhook');
        return $this->makeRequest($apiUrl, []);
    }

    /**
     * Récupère les informations du webhook
     */
    public function getWebhookInfo(string $token): ?array
    {
        $apiUrl = $this->buildUrl($token, 'getWebhookInfo');
        return $this->makeRequest($apiUrl, []);
    }

    /**
     * Envoie un message texte
     */
    public function sendMessage(string $token, int $chatId, string $text, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendMessage');
        
        $params = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown', // Par défaut
        ], $options);
        
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie une photo
     */
    public function sendPhoto(string $token, int $chatId, string $photo, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendPhoto');
        $params = array_merge([
            'chat_id' => $chatId,
            'photo' => $photo,
        ], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie un document
     */
    public function sendDocument(string $token, int $chatId, string $document, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendDocument');
        $params = array_merge([
            'chat_id' => $chatId,
            'document' => $document,
        ], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie une vidéo
     */
    public function sendVideo(string $token, int $chatId, string $video, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendVideo');
        $params = array_merge([
            'chat_id' => $chatId,
            'video' => $video,
        ], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie un audio
     */
    public function sendAudio(string $token, int $chatId, string $audio, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendAudio');
        $params = array_merge([
            'chat_id' => $chatId,
            'audio' => $audio,
        ], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie un message vocal
     */
    public function sendVoice(string $token, int $chatId, string $voice, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendVoice');
        $params = array_merge([
            'chat_id' => $chatId,
            'voice' => $voice,
        ], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie un sticker
     */
    public function sendSticker(string $token, int $chatId, string $sticker, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendSticker');
        $params = array_merge([
            'chat_id' => $chatId,
            'sticker' => $sticker,
        ], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie une localisation
     */
    public function sendLocation(string $token, int $chatId, float $latitude, float $longitude, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendLocation');
        $params = array_merge([
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie un contact
     */
    public function sendContact(string $token, int $chatId, string $phoneNumber, string $firstName, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendContact');
        $params = array_merge([
            'chat_id' => $chatId,
            'phone_number' => $phoneNumber,
            'first_name' => $firstName,
        ], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Répond à une callback query (bouton inline cliqué)
     */
    public function answerCallbackQuery(string $token, string $callbackQueryId, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'answerCallbackQuery');
        $params = array_merge([
            'callback_query_id' => $callbackQueryId,
        ], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Édite un message existant
     */
    public function editMessageText(string $token, int $chatId, int $messageId, string $text, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'editMessageText');
        $params = array_merge([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
        ], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Édite le clavier inline d'un message
     */
    public function editMessageReplyMarkup(string $token, int $chatId, int $messageId, array $replyMarkup = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'editMessageReplyMarkup');
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => $replyMarkup,
        ];
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Supprime un message
     */
    public function deleteMessage(string $token, int $chatId, int $messageId): ?array
    {
        $apiUrl = $this->buildUrl($token, 'deleteMessage');
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ];
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie une action de chat (typing, upload_photo, etc.)
     */
    public function sendChatAction(string $token, int $chatId, string $action): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendChatAction');
        $params = [
            'chat_id' => $chatId,
            'action' => $action, // typing, upload_photo, record_video, etc.
        ];
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Récupère les informations d'un fichier
     */
    public function getFile(string $token, string $fileId): ?array
    {
        $apiUrl = $this->buildUrl($token, 'getFile');
        $params = ['file_id' => $fileId];
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Récupère les informations du bot
     */
    public function getMe(string $token): ?array
    {
        $apiUrl = $this->buildUrl($token, 'getMe');
        return $this->makeRequest($apiUrl, []);
    }

    /**
     * Récupère les informations d'un chat
     */
    public function getChat(string $token, int $chatId): ?array
    {
        $apiUrl = $this->buildUrl($token, 'getChat');
        $params = ['chat_id' => $chatId];
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Récupère le nombre de membres d'un chat
     */
    public function getChatMemberCount(string $token, int $chatId): ?array
    {
        $apiUrl = $this->buildUrl($token, 'getChatMemberCount');
        $params = ['chat_id' => $chatId];
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Récupère les informations d'un membre du chat
     */
    public function getChatMember(string $token, int $chatId, int $userId): ?array
    {
        $apiUrl = $this->buildUrl($token, 'getChatMember');
        $params = [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ];
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Fait une requête HTTP à l'API Telegram
     */
    protected function makeRequest(string $url, array $params): ?array
    {
        try {
            $response = Http::timeout(10)->post($url, $params);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['ok']) && $data['ok'] === true) {
                    return $data;
                }
                
                Log::warning('Telegram API returned ok=false', [
                    'response' => $data,
                ]);
                
                return $data;
            }
            
            Log::error('Telegram API HTTP Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $url,
            ]);
            
            return null;
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Telegram API Connection Error', [
                'error' => $e->getMessage(),
                'url' => $url,
            ]);
            return null;
            
        } catch (\Exception $e) {
            Log::error('Telegram API Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $url,
            ]);
            return null;
        }
    }

    /**
     * Formate un texte pour Telegram Markdown
     */
    public function formatMarkdown(string $text): string
    {
        // Échapper les caractères spéciaux Markdown
        $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        
        foreach ($specialChars as $char) {
            $text = str_replace($char, '\\' . $char, $text);
        }
        
        return $text;
    }

    /**
     * Formate un texte pour Telegram HTML
     */
    public function formatHTML(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}
