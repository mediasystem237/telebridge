<?php

namespace Mbindi\Telebridge;

use Mbindi\Telebridge\Services\TelegramClient;
use Mbindi\Telebridge\Data\BotInfo;
use Mbindi\Telebridge\Data\WebhookInfo;

class TeleBridgeManager
{
    protected TelegramClient $client;
    protected array $bots = [];
    protected ?string $currentBot = null;

    public function __construct(TelegramClient $client)
    {
        $this->client = $client;
        $this->loadBotsFromConfig();
    }

    /**
     * Charge les bots depuis la configuration
     */
    protected function loadBotsFromConfig(): void
    {
        $this->bots = config('telebridge.bots', []);
    }

    /**
     * Sélectionne un bot spécifique
     * 
     * @param string $name Nom du bot dans la config
     * @return TelegramClient
     */
    public function bot(string $name = 'default'): TelegramClient
    {
        $this->currentBot = $name;
        return $this->client;
    }

    /**
     * Récupère le token du bot actuel
     */
    protected function getCurrentToken(): ?string
    {
        $botName = $this->currentBot ?? 'default';
        return $this->bots[$botName]['token'] ?? config('telebridge.bot.token');
    }

    /**
     * Récupère les informations du bot (avec DTO)
     */
    public function getBotInfo(string $token): ?BotInfo
    {
        $response = $this->client->getMe($token);
        return BotInfo::fromResponse($response);
    }

    /**
     * Récupère les informations du webhook (avec DTO)
     */
    public function getWebhookData(string $token): ?WebhookInfo
    {
        $response = $this->client->getWebhookInfo($token);
        return WebhookInfo::fromResponse($response);
    }

    /**
     * Proxy vers TelegramClient
     */
    public function __call($method, $parameters)
    {
        return $this->client->{$method}(...$parameters);
    }
}

