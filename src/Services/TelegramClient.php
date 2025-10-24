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

    public function sendMessage(string $token, int $chatId, string $text): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendMessage');
        $response = Http::post($apiUrl, [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
        return $response->json();
    }
    
    // Future methods: sendPhoto, sendDocument, etc.
}
