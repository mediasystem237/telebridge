<?php

namespace Mbindi\Telebridge\Console;

use Illuminate\Console\Command;
use Mbindi\Telebridge\Services\TelegramClient;
use Mbindi\Telebridge\Models\TelegramBot;

class SetupCommandsCommand extends Command
{
    protected $signature = 'telebridge:setup-commands 
                            {--bot= : ID du bot ou token à utiliser}
                            {--token= : Token du bot}
                            {--file= : Fichier JSON avec les commandes}
                            {--interactive : Mode interactif}';

    protected $description = 'Configure les commandes du bot pour l\'autocomplétion Telegram';

    public function handle(TelegramClient $client): int
    {
        $token = $this->getToken();
        if (!$token) {
            $this->error('❌ Aucun token trouvé');
            return self::FAILURE;
        }

        // Mode interactif
        if ($this->option('interactive')) {
            return $this->interactiveMode($client, $token);
        }

        // Mode fichier
        if ($file = $this->option('file')) {
            return $this->fileMode($client, $token, $file);
        }

        // Mode par défaut : commandes standards
        return $this->defaultMode($client, $token);
    }

    /**
     * Mode interactif
     */
    protected function interactiveMode(TelegramClient $client, string $token): int
    {
        $this->info('🎨 Mode interactif - Configuration des commandes');
        $this->newLine();

        $commands = [];
        
        while (true) {
            $command = $this->ask('Commande (sans /, ou vide pour terminer)');
            
            if (empty($command)) {
                break;
            }

            $description = $this->ask('Description de la commande');

            $commands[] = [
                'command' => $command,
                'description' => $description,
            ];

            $this->info("✓ Ajouté: /{$command}");
        }

        if (empty($commands)) {
            $this->warn('Aucune commande ajoutée');
            return self::SUCCESS;
        }

        return $this->setCommands($client, $token, $commands);
    }

    /**
     * Mode fichier JSON
     */
    protected function fileMode(TelegramClient $client, string $token, string $file): int
    {
        if (!file_exists($file)) {
            $this->error("❌ Fichier non trouvé : {$file}");
            return self::FAILURE;
        }

        $json = file_get_contents($file);
        $commands = json_decode($json, true);

        if (!$commands || !is_array($commands)) {
            $this->error('❌ Format JSON invalide');
            return self::FAILURE;
        }

        return $this->setCommands($client, $token, $commands);
    }

    /**
     * Mode par défaut : commandes standards
     */
    protected function defaultMode(TelegramClient $client, string $token): int
    {
        $commands = [
            ['command' => 'start', 'description' => 'Démarrer la conversation'],
            ['command' => 'help', 'description' => 'Afficher l\'aide'],
        ];

        $this->info('📋 Configuration des commandes par défaut');
        
        return $this->setCommands($client, $token, $commands);
    }

    /**
     * Envoie les commandes à Telegram
     */
    protected function setCommands(TelegramClient $client, string $token, array $commands): int
    {
        $this->newLine();
        $this->info('📤 Envoi à Telegram...');

        $url = "https://api.telegram.org/bot{$token}/setMyCommands";
        
        $response = \Illuminate\Support\Facades\Http::post($url, [
            'commands' => json_encode($commands)
        ]);

        if (!$response->successful()) {
            $this->error('❌ Échec de la configuration');
            
            $error = $client->getLastError();
            if ($error) {
                $this->line("   Code: {$error['error_code']}");
                $this->line("   Message: {$error['description']}");
            }
            
            return self::FAILURE;
        }

        $this->info('✅ Commandes configurées avec succès !');
        $this->newLine();
        
        $this->table(
            ['Commande', 'Description'],
            array_map(fn($cmd) => ['/' . $cmd['command'], $cmd['description']], $commands)
        );

        $this->newLine();
        $this->comment('💡 Les utilisateurs verront maintenant ces commandes en autocomplétion');

        return self::SUCCESS;
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
}

