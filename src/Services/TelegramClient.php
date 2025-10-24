<?php

namespace Mbindi\Telebridge\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramClient
{
    protected $apiBaseUrl;
    protected $lastError = null;
    protected $lastResponse = null;

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
     * Configure le webhook avec options avancées
     * 
     * @param string $token Token du bot
     * @param string $url URL du webhook
     * @param array $options Options:
     *   - certificate: Certificat SSL public (pour self-signed)
     *   - ip_address: Adresse IP fixe du serveur
     *   - max_connections: Nombre max de connexions simultanées (1-100, défaut: 40)
     *   - allowed_updates: Types de mises à jour à recevoir (ex: ['message', 'callback_query'])
     *   - drop_pending_updates: Supprimer les mises à jour en attente
     *   - secret_token: Token secret pour validation (1-256 caractères)
     */
    public function setWebhook(string $token, string $url, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'setWebhook');
        $params = array_merge(['url' => $url], $options);
        
        // Convertir allowed_updates en JSON si c'est un array
        if (isset($params['allowed_updates']) && is_array($params['allowed_updates'])) {
            $params['allowed_updates'] = json_encode($params['allowed_updates']);
        }
        
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Supprime le webhook
     */
    public function deleteWebhook(string $token, bool $dropPendingUpdates = false): ?array
    {
        $apiUrl = $this->buildUrl($token, 'deleteWebhook');
        $params = ['drop_pending_updates' => $dropPendingUpdates];
        return $this->makeRequest($apiUrl, $params);
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
        $params = array_merge(['chat_id' => $chatId, 'photo' => $photo], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie un document
     */
    public function sendDocument(string $token, int $chatId, string $document, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendDocument');
        $params = array_merge(['chat_id' => $chatId, 'document' => $document], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie une vidéo
     */
    public function sendVideo(string $token, int $chatId, string $video, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendVideo');
        $params = array_merge(['chat_id' => $chatId, 'video' => $video], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie un audio
     */
    public function sendAudio(string $token, int $chatId, string $audio, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendAudio');
        $params = array_merge(['chat_id' => $chatId, 'audio' => $audio], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie un message vocal
     */
    public function sendVoice(string $token, int $chatId, string $voice, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendVoice');
        $params = array_merge(['chat_id' => $chatId, 'voice' => $voice], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Envoie un sticker
     */
    public function sendSticker(string $token, int $chatId, string $sticker, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'sendSticker');
        $params = array_merge(['chat_id' => $chatId, 'sticker' => $sticker], $options);
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
     * Répond à une callback query
     */
    public function answerCallbackQuery(string $token, string $callbackQueryId, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'answerCallbackQuery');
        $params = array_merge(['callback_query_id' => $callbackQueryId], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Répond à une inline query
     * 
     * @param string $token Token du bot
     * @param string $inlineQueryId ID de l'inline query
     * @param array $results Tableau de résultats (max 50)
     * @param array $options Options additionnelles
     */
    public function answerInlineQuery(string $token, string $inlineQueryId, array $results, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'answerInlineQuery');
        $params = array_merge([
            'inline_query_id' => $inlineQueryId,
            'results' => json_encode($results),
        ], $options);
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Répond à une pre-checkout query (e-commerce)
     * 
     * @param string $token Token du bot
     * @param string $preCheckoutQueryId ID de la pre-checkout query
     * @param bool $ok True si tout est OK, False sinon
     * @param string|null $errorMessage Message d'erreur si ok=false
     */
    public function answerPreCheckoutQuery(string $token, string $preCheckoutQueryId, bool $ok, ?string $errorMessage = null): ?array
    {
        $apiUrl = $this->buildUrl($token, 'answerPreCheckoutQuery');
        $params = [
            'pre_checkout_query_id' => $preCheckoutQueryId,
            'ok' => $ok,
        ];
        
        if (!$ok && $errorMessage) {
            $params['error_message'] = $errorMessage;
        }
        
        return $this->makeRequest($apiUrl, $params);
    }

    /**
     * Répond à une shipping query (e-commerce)
     */
    public function answerShippingQuery(string $token, string $shippingQueryId, bool $ok, array $options = []): ?array
    {
        $apiUrl = $this->buildUrl($token, 'answerShippingQuery');
        $params = array_merge([
            'shipping_query_id' => $shippingQueryId,
            'ok' => $ok,
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
     * Construit l'URL de téléchargement d'un fichier
     */
    public function getFileDownloadUrl(string $token, string $filePath): string
    {
        return "https://api.telegram.org/file/bot{$token}/{$filePath}";
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
     * Récupère la dernière erreur survenue
     * 
     * @return array|null ['error_code' => int, 'description' => string, 'parameters' => array] ou null
     */
    public function getLastError(): ?array
    {
        return $this->lastError;
    }

    /**
     * Récupère la dernière réponse HTTP brute
     * 
     * @return array|null ['status' => int, 'body' => string, 'headers' => array] ou null
     */
    public function getLastResponse(): ?array
    {
        return $this->lastResponse;
    }

    /**
     * Vérifie si une erreur spécifique s'est produite
     */
    public function hasError(string $errorDescription): bool
    {
        if (!$this->lastError) {
            return false;
        }
        
        return str_contains(
            strtolower($this->lastError['description'] ?? ''),
            strtolower($errorDescription)
        );
    }

    /**
     * Réinitialise les erreurs et réponses
     */
    public function resetErrors(): void
    {
        $this->lastError = null;
        $this->lastResponse = null;
    }

    /**
     * Fait une requête HTTP à l'API Telegram
     */
    protected function makeRequest(string $url, array $params): ?array
    {
        // Réinitialiser les erreurs précédentes
        $this->resetErrors();
        
        try {
            $response = Http::timeout(10)->post($url, $params);
            
            // Stocker la réponse brute
            $this->lastResponse = [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
            ];
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['ok']) && $data['ok'] === true) {
                    return $data;
                }
                
                // Telegram a retourné ok=false
                $this->lastError = [
                    'error_code' => $data['error_code'] ?? 0,
                    'description' => $data['description'] ?? 'Unknown error',
                    'parameters' => $data['parameters'] ?? [],
                ];
                
                Log::warning('Telegram API returned ok=false', [
                    'error' => $this->lastError,
                    'url' => $this->sanitizeUrl($url),
                ]);
                
                return null;
            }
            
            // Erreur HTTP
            $this->lastError = [
                'error_code' => $response->status(),
                'description' => "HTTP Error: " . $response->status(),
                'parameters' => [],
            ];
            
            Log::error('Telegram API HTTP Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $this->sanitizeUrl($url),
            ]);
            
            return null;
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->lastError = [
                'error_code' => 0,
                'description' => 'Connection error: ' . $e->getMessage(),
                'parameters' => [],
            ];
            
            Log::error('Telegram API Connection Error', [
                'error' => $e->getMessage(),
                'url' => $this->sanitizeUrl($url),
            ]);
            return null;
            
        } catch (\Exception $e) {
            $this->lastError = [
                'error_code' => 0,
                'description' => 'Exception: ' . $e->getMessage(),
                'parameters' => [],
            ];
            
            Log::error('Telegram API Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $this->sanitizeUrl($url),
            ]);
            return null;
        }
    }

    /**
     * Sanitize l'URL pour le logging (retire le token)
     */
    protected function sanitizeUrl(string $url): string
    {
        return preg_replace('/bot\d+:[^\/]+/', 'bot***TOKEN***', $url);
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
