<?php

namespace Mbindi\Telebridge\Services;

use Illuminate\Support\Facades\Http;

class TelegramClient
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = 'https://api.telegram.org/bot';
    }

    protected function buildUrl(string $token, string $method): string
    {
        return "{$this->apiBaseUrl}{$token}/{$method}";
    }

    public function setWebhook(string $token, string $url): ?array
    {
        $apiUrl = $this->buildUrl($token, 'setWebhook');
        $response = Http::post($apiUrl, ['url' => $url]);
        return $response->json();
    }

    public function sendMessage(string $token, int $chatId, string $text, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendMessage');
        $response = Http::post($apiUrl, array_merge([
            'chat_id' => $chatId,
            'text' => $text,
        ], $options));
        return $response->json();
    }

    public function sendPhoto(string $token, int $chatId, string $photo, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendPhoto');
        $response = Http::post($apiUrl, array_merge([
            'chat_id' => $chatId,
            'photo' => $photo,
        ], $options));
        return $response->json();
    }

    public function sendDocument(string $token, int $chatId, string $document, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendDocument');
        $response = Http::post($apiUrl, array_merge([
            'chat_id' => $chatId,
            'document' => $document,
        ], $options));
        return $response->json();
    }

    public function sendVideo(string $token, int $chatId, string $video, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendVideo');
        $response = Http::post($apiUrl, array_merge([
            'chat_id' => $chatId,
            'video' => $video,
        ], $options));
        return $response->json();
    }

    public function answerCallbackQuery(string $token, string $callbackQueryId, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'answerCallbackQuery');
        $response = Http::post($apiUrl, array_merge([
            'callback_query_id' => $callbackQueryId,
        ], $options));
        return $response->json();
    }
}
