<?php

namespace Mbindi\Telebridge\Services;

use Illuminate\Support\Facades\Log;
use Mbindi\Telebridge\Models\TelegramBot;
use Mbindi\Telebridge\Models\TelegramMessage;
use Mbindi\Telebridge\Models\TelegramUser;

class MessageRouter
{
    protected $telegramClient;

    public function __construct(TelegramClient $telegramClient)
    {
        $this->telegramClient = $telegramClient;
    }

    /**
     * Gère une mise à jour Telegram (webhook)
     */
    public function handleUpdate(TelegramBot $bot, array $updateData): void
    {
        Log::info('TeleBridge: Handling update for bot ' . $bot->name, $updateData);

        if (isset($updateData['message'])) {
            $this->handleMessage($bot, $updateData['message']);
        } elseif (isset($updateData['callback_query'])) {
            $this->handleCallbackQuery($bot, $updateData['callback_query']);
        }
    }

    /**
     * Gère un message entrant
     */
    protected function handleMessage(TelegramBot $bot, array $messageData): void
    {
        $telegramUserId = $messageData['from']['id'];
        $chatId = $messageData['chat']['id'];
        $text = $messageData['text'] ?? '';

        // 1. Créer/mettre à jour l'utilisateur Telegram
        $telegramUser = TelegramUser::firstOrCreate(
            ['telegram_id' => $telegramUserId],
            [
                'username' => $messageData['from']['username'] ?? null,
                'first_name' => $messageData['from']['first_name'] ?? null,
                'last_name' => $messageData['from']['last_name'] ?? null,
            ]
        );
        $telegramUser->last_seen = now();
        $telegramUser->save();

        // 2. Enregistrer le message dans la base de données
        $telegramMessage = TelegramMessage::create([
            'bot_id' => $bot->id,
            'user_id' => $telegramUser->telegram_id,
            'type' => $this->detectMessageType($messageData),
            'content' => $this->extractContent($messageData),
        ]);

        // 3. Gérer les commandes système basiques (optionnel, synchrone)
        if (str_starts_with($text, '/')) {
            $response = $this->handleCommand($text);
            if ($response) {
                $this->telegramClient->sendMessage($bot->token, $chatId, $response);
                
                $telegramMessage->response = $response;
                $telegramMessage->processed_at = now();
                $telegramMessage->save();
                
                return;
            }
        }

        // 4. ✅ DISPATCHER AU BACKEND LARAVEL (Job asynchrone)
        // Toute la logique IA/métier se fait dans le backend
        try {
            // Vérifier que la classe Job existe
            if (class_exists('\App\Jobs\ProcessTelegramMessage')) {
                \App\Jobs\ProcessTelegramMessage::dispatch($bot, $telegramMessage, $chatId);
                Log::info('TeleBridge: Message dispatched to Laravel backend', [
                    'message_id' => $telegramMessage->id
                ]);
            } else {
                // Fallback si le Job n'existe pas encore
                Log::warning('TeleBridge: ProcessTelegramMessage job not found, sending default response');
                $this->telegramClient->sendMessage(
                    $bot->token,
                    $chatId,
                    "Message reçu ! Le système de traitement sera bientôt disponible."
                );
            }
        } catch (\Exception $e) {
            Log::error('TeleBridge: Error dispatching message', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Gère un callback query (clic sur bouton inline)
     */
    protected function handleCallbackQuery(TelegramBot $bot, array $callbackQueryData): void
    {
        $callbackQueryId = $callbackQueryData['id'];
        $callbackData = $callbackQueryData['data'] ?? '';
        $chatId = $callbackQueryData['message']['chat']['id'] ?? null;
        $telegramUserId = $callbackQueryData['from']['id'];

        // Acknowledge immédiatement (requis par Telegram)
        $this->telegramClient->answerCallbackQuery($bot->token, $callbackQueryId, [
            'text' => 'Traitement en cours...'
        ]);

        // Enregistrer comme message de type callback
        $telegramMessage = TelegramMessage::create([
            'bot_id' => $bot->id,
            'user_id' => $telegramUserId,
            'type' => 'callback_query',
            'content' => $callbackData,
        ]);

        // Dispatcher au backend pour traitement
        try {
            if (class_exists('\App\Jobs\ProcessTelegramMessage')) {
                \App\Jobs\ProcessTelegramMessage::dispatchCallback($bot, $telegramMessage, $chatId, $callbackData);
            }
        } catch (\Exception $e) {
            Log::error('TeleBridge: Error dispatching callback', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Gère les commandes système de base (synchrone)
     * Seulement les commandes très basiques
     */
    protected function handleCommand(string $text): ?string
    {
        return match ($text) {
            '/start' => "👋 Bienvenue ! Je suis votre assistant intelligent.\n\nPosez-moi vos questions, je suis là pour vous aider.",
            '/help' => "❓ **Aide**\n\nVous pouvez me poser n'importe quelle question concernant nos services.\n\nCommandes disponibles:\n/start - Démarrer\n/help - Afficher l'aide",
            default => null, // Autres commandes seront traitées par le backend
        };
    }

    /**
     * Détecte le type de message
     */
    protected function detectMessageType(array $messageData): string
    {
        if (isset($messageData['text'])) return 'text';
        if (isset($messageData['photo'])) return 'photo';
        if (isset($messageData['document'])) return 'document';
        if (isset($messageData['audio'])) return 'audio';
        if (isset($messageData['voice'])) return 'voice';
        if (isset($messageData['video'])) return 'video';
        if (isset($messageData['sticker'])) return 'sticker';
        if (isset($messageData['location'])) return 'location';
        if (isset($messageData['contact'])) return 'contact';
        return 'unknown';
    }

    /**
     * Extrait le contenu du message
     */
    protected function extractContent(array $messageData): string
    {
        if (isset($messageData['text'])) {
            return $messageData['text'];
        }
        
        if (isset($messageData['caption'])) {
            return $messageData['caption'];
        }
        
        // Pour les autres types, stocker les infos essentielles en JSON
        $content = [];
        
        if (isset($messageData['photo'])) {
            $content['file_id'] = end($messageData['photo'])['file_id'] ?? null;
            $content['caption'] = $messageData['caption'] ?? '';
        }
        
        if (isset($messageData['document'])) {
            $content['file_id'] = $messageData['document']['file_id'] ?? null;
            $content['file_name'] = $messageData['document']['file_name'] ?? null;
        }
        
        if (isset($messageData['location'])) {
            $content['latitude'] = $messageData['location']['latitude'] ?? null;
            $content['longitude'] = $messageData['location']['longitude'] ?? null;
        }

        return !empty($content) ? json_encode($content) : json_encode($messageData);
    }
}
