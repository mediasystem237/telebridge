<?php

namespace Mbindi\Telebridge\Console;

use Illuminate\Console\Command;
use Mbindi\Telebridge\Services\TelegramClient;
use Mbindi\Telebridge\Models\TelegramBot;

class TestTelegramConnection extends Command
{
    protected $signature = 'telebridge:test 
                            {chat_id? : ID du chat Telegram} 
                            {message? : Message à envoyer}
                            {--bot= : ID ou token du bot à utiliser}
                            {--token= : Token du bot (prioritaire sur --bot)}';

    protected $description = 'Teste la connexion à l\'API Telegram en envoyant un message';

    public function handle(TelegramClient $client): int
    {
        $this->info('🔍 Test de connexion TeleBridge → Telegram API');
        $this->newLine();

        // 1. Déterminer le token à utiliser
        $token = $this->getToken();
        if (!$token) {
            $this->error('❌ Aucun token trouvé. Utilisez --token ou --bot, ou configurez TELEGRAM_BOT_TOKEN');
            return self::FAILURE;
        }

        // 2. Tester getMe (informations du bot)
        $this->info('📡 Test 1/3 : Récupération des informations du bot...');
        $botInfo = $client->getMe($token);

        if (!$botInfo) {
            $this->error('❌ Échec de la connexion');
            $this->displayError($client);
            return self::FAILURE;
        }

        $this->info('✅ Bot connecté :');
        $this->line("   Nom: {$botInfo['result']['first_name']}");
        $this->line("   Username: @{$botInfo['result']['username']}");
        $this->line("   ID: {$botInfo['result']['id']}");
        $this->newLine();

        // 3. Tester getWebhookInfo
        $this->info('📡 Test 2/3 : Vérification du webhook...');
        $webhookInfo = $client->getWebhookInfo($token);

        if ($webhookInfo) {
            $url = $webhookInfo['result']['url'] ?? 'Non configuré';
            $pendingCount = $webhookInfo['result']['pending_update_count'] ?? 0;
            
            $this->info('✅ Webhook :');
            $this->line("   URL: {$url}");
            $this->line("   Updates en attente: {$pendingCount}");
            
            if ($webhookInfo['result']['last_error_date'] ?? false) {
                $this->warn("   ⚠️  Dernière erreur: " . ($webhookInfo['result']['last_error_message'] ?? 'Unknown'));
            }
        }
        $this->newLine();

        // 4. Tester l'envoi de message (si chat_id fourni)
        $chatId = $this->argument('chat_id');
        
        if (!$chatId) {
            $this->info('💡 Conseil : Ajoutez un chat_id pour tester l\'envoi de message :');
            $this->line('   php artisan telebridge:test <chat_id> "Bonjour !"');
            $this->newLine();
            $this->info('✅ Tests de base réussis !');
            return self::SUCCESS;
        }

        $this->info('📡 Test 3/3 : Envoi d\'un message de test...');
        $message = $this->argument('message') ?? '🎉 Test de connexion TeleBridge réussi !';

        $result = $client->sendMessage($token, (int) $chatId, $message, [
            'parse_mode' => 'Markdown'
        ]);

        if (!$result) {
            $this->error('❌ Échec de l\'envoi du message');
            $this->displayError($client);
            return self::FAILURE;
        }

        $this->info('✅ Message envoyé avec succès !');
        $this->line("   Message ID: {$result['result']['message_id']}");
        $this->line("   Chat ID: {$result['result']['chat']['id']}");
        $this->newLine();

        $this->info('🎉 Tous les tests sont passés avec succès !');
        return self::SUCCESS;
    }

    /**
     * Récupère le token à utiliser
     */
    protected function getToken(): ?string
    {
        // 1. Option --token (prioritaire)
        if ($token = $this->option('token')) {
            return $token;
        }

        // 2. Option --bot (ID ou token)
        if ($botId = $this->option('bot')) {
            // Si c'est un nombre, chercher dans la DB
            if (is_numeric($botId)) {
                $bot = TelegramBot::find($botId);
                if ($bot) {
                    return $bot->token;
                }
            }
            // Sinon considérer que c'est un token
            return $botId;
        }

        // 3. Variable d'environnement
        if ($token = config('telebridge.bot.token')) {
            return $token;
        }

        return null;
    }

    /**
     * Affiche les détails de l'erreur
     */
    protected function displayError(TelegramClient $client): void
    {
        $error = $client->getLastError();
        
        if (!$error) {
            $this->error('Erreur inconnue (pas de détails disponibles)');
            return;
        }

        $this->newLine();
        $this->error('Détails de l\'erreur :');
        $this->line("   Code: {$error['error_code']}");
        $this->line("   Description: {$error['description']}");
        
        if (!empty($error['parameters'])) {
            $this->line("   Paramètres: " . json_encode($error['parameters']));
        }

        $this->newLine();
        $this->warn('💡 Vérifiez :');
        $this->line('   • Que le token est correct');
        $this->line('   • Que le bot n\'a pas été supprimé sur BotFather');
        $this->line('   • Que le chat_id est valide');
        $this->line('   • Que le bot peut envoyer des messages à ce chat');
    }
}

