<?php

namespace Mbindi\Telebridge\Console;

use Illuminate\Console\Command;
use Mbindi\Telebridge\Services\TelegramClient;
use Mbindi\Telebridge\Models\TelegramBot;
use Mbindi\Telebridge\Services\MessageRouter;

class PollingCommand extends Command
{
    protected $signature = 'telebridge:polling 
                            {--bot= : ID du bot ou token à utiliser}
                            {--token= : Token du bot (prioritaire sur --bot)}
                            {--timeout=30 : Timeout du long polling (secondes)}
                            {--limit=100 : Nombre max de updates par requête}';

    protected $description = 'Démarre le long polling pour développement local (sans webhook)';

    protected $offset = 0;
    protected $running = true;

    public function handle(TelegramClient $client, MessageRouter $router): int
    {
        // Obtenir le token
        $token = $this->getToken();
        if (!$token) {
            $this->error('❌ Aucun token trouvé. Utilisez --token ou --bot');
            return self::FAILURE;
        }

        // Obtenir le bot
        $bot = $this->getBot($token);
        if (!$bot) {
            $this->error('❌ Bot non trouvé en base de données');
            return self::FAILURE;
        }

        // Vérifier la connexion
        $botInfo = $client->getMe($token);
        if (!$botInfo) {
            $this->error('❌ Impossible de se connecter au bot');
            return self::FAILURE;
        }

        $this->info('🚀 Long Polling démarré pour : ' . $botInfo['result']['first_name']);
        $this->info('   Username: @' . $botInfo['result']['username']);
        $this->info('   Mode: Développement local (pas de webhook requis)');
        $this->newLine();
        $this->warn('⚠️  Appuyez sur Ctrl+C pour arrêter');
        $this->newLine();

        // Configuration du signal handler
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'handleShutdown']);
            pcntl_signal(SIGINT, [$this, 'handleShutdown']);
        }

        $timeout = (int) $this->option('timeout');
        $limit = (int) $this->option('limit');

        // Boucle de polling
        while ($this->running) {
            try {
                // Appeler getUpdates
                $updates = $this->getUpdates($client, $token, $timeout, $limit);

                if ($updates && isset($updates['result'])) {
                    foreach ($updates['result'] as $update) {
                        $this->processUpdate($router, $bot, $update);
                        
                        // Mettre à jour l'offset
                        $this->offset = $update['update_id'] + 1;
                    }
                }

                // Permettre au signal handler de s'exécuter
                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }

            } catch (\Exception $e) {
                $this->error('❌ Erreur : ' . $e->getMessage());
                
                if (!$this->confirm('Continuer le polling ?', true)) {
                    $this->running = false;
                }
            }
        }

        $this->newLine();
        $this->info('✅ Polling arrêté proprement');
        return self::SUCCESS;
    }

    /**
     * Récupère les updates via long polling
     */
    protected function getUpdates(TelegramClient $client, string $token, int $timeout, int $limit): ?array
    {
        $url = "https://api.telegram.org/bot{$token}/getUpdates";
        
        $params = [
            'offset' => $this->offset,
            'timeout' => $timeout,
            'limit' => $limit,
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::timeout($timeout + 5)
                ->post($url, $params);

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            $this->warn("⚠️  Timeout ou erreur réseau (normal en long polling)");
            return null;
        }
    }

    /**
     * Traite une update
     */
    protected function processUpdate(MessageRouter $router, TelegramBot $bot, array $update): void
    {
        $updateId = $update['update_id'];
        
        // Afficher l'info
        if (isset($update['message'])) {
            $from = $update['message']['from']['username'] ?? $update['message']['from']['first_name'];
            $text = $update['message']['text'] ?? '[media]';
            $this->info("📥 #{$updateId} Message de @{$from}: {$text}");
        } elseif (isset($update['callback_query'])) {
            $from = $update['callback_query']['from']['username'] ?? $update['callback_query']['from']['first_name'];
            $data = $update['callback_query']['data'] ?? '';
            $this->info("🖱️  #{$updateId} Callback de @{$from}: {$data}");
        } elseif (isset($update['inline_query'])) {
            $from = $update['inline_query']['from']['username'] ?? $update['inline_query']['from']['first_name'];
            $query = $update['inline_query']['query'] ?? '';
            $this->info("🔍 #{$updateId} Inline query de @{$from}: {$query}");
        }

        // Router l'update (comme le webhook)
        try {
            $router->handleUpdate($bot, $update);
            $this->comment("   ✓ Traité");
        } catch (\Exception $e) {
            $this->error("   ✗ Erreur : " . $e->getMessage());
        }
    }

    /**
     * Gère l'arrêt propre
     */
    public function handleShutdown(): void
    {
        $this->running = false;
        $this->newLine();
        $this->warn('⚠️  Arrêt en cours...');
    }

    /**
     * Récupère le token
     */
    protected function getToken(): ?string
    {
        if ($token = $this->option('token')) {
            return $token;
        }

        if ($botId = $this->option('bot')) {
            if (is_numeric($botId)) {
                $bot = TelegramBot::find($botId);
                if ($bot) {
                    return $bot->token;
                }
            }
            return $botId;
        }

        return config('telebridge.bot.token');
    }

    /**
     * Récupère le bot
     */
    protected function getBot(string $token): ?TelegramBot
    {
        $bot = TelegramBot::where('token', $token)->first();

        if (!$bot) {
            // Créer un bot temporaire pour le développement
            $this->warn('⚠️  Bot non trouvé en base, création d\'un bot temporaire...');
            
            $bot = new TelegramBot([
                'token' => $token,
                'name' => 'Development Bot',
                'is_active' => true,
            ]);
            
            // Ne pas sauvegarder en base, juste utiliser l'instance
        }

        return $bot;
    }
}

