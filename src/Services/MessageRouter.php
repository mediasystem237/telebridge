<?php

namespace Mbindi\Telebridge\Services;

use Illuminate\Support\Facades\Log;
use Mbindi\Telebridge\Models\TelegramBot;
use Mbindi\Telebridge\Models\TelegramMessage;
use Mbindi\Telebridge\Models\TelegramUser;

class MessageRouter
{
    protected $intentDetector;
    protected $responseEngine;
    protected $telegramClient;

    public function __construct(IntentDetector $intentDetector, ResponseEngine $responseEngine, TelegramClient $telegramClient)
    {
        $this->intentDetector = $intentDetector;
        $this->responseEngine = $responseEngine;
        $this->telegramClient = $telegramClient;
    }

    public function handleUpdate(TelegramBot $bot, array $updateData): void
    {
        Log::info('TeleBridge: Handling update for bot ' . $bot->name, $updateData);

        if (isset($updateData['message'])) {
            $this->handleMessage($bot, $updateData['message']);
        } elseif (isset($updateData['callback_query'])) {
            $this->handleCallbackQuery($bot, $updateData['callback_query']);
        }
        // Add more update types here (edited_message, channel_post, etc.)
    }

    protected function handleMessage(TelegramBot $bot, array $messageData): void
    {
        $telegramUserId = $messageData['from']['id'];
        $chatId = $messageData['chat']['id'];
        $text = $messageData['text'] ?? '';

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

        $telegramMessage = TelegramMessage::create([
            'bot_id' => $bot->id,
            'user_id' => $telegramUser->telegram_id,
            'type' => 'text', // For now, assume text. Will need to parse other types later.
            'content' => $text,
        ]);

        $response_data = str_starts_with($text, '/')
            ? $this->handleCommand($text)
            : $this->handleNaturalLanguage($text);

        if (isset($response_data['text'])) {
            $this->telegramClient->sendMessage(
                $bot->token,
                $chatId,
                $response_data['text'],
                ['reply_markup' => $response_data['reply_markup'] ?? null]
            );
        }

        $telegramMessage->response = $response_data['text'] ?? null; // Store only text in DB for now
        $telegramMessage->processed_at = now();
        $telegramMessage->save();
    }

    protected function handleCallbackQuery(TelegramBot $bot, array $callbackQueryData): void
    {
        $callbackQueryId = $callbackQueryData['id'];
        $callbackData = $callbackQueryData['data'] ?? '';
        $chatId = $callbackQueryData['message']['chat']['id'] ?? null;
        $telegramUserId = $callbackQueryData['from']['id'];

        // Acknowledge the callback query
        $this->telegramClient->answerCallbackQuery($bot->token, $callbackQueryId, ['text' => 'Processing...']);

        // For now, just respond with the callback data.
        // In a real scenario, this would trigger specific actions based on $callbackData.
        $response_text = "You clicked: " . $callbackData;

        if ($chatId && $response_text) {
            $this->telegramClient->sendMessage(
                $bot->token,
                $chatId,
                $response_text
            );
        }
        
        // Log the callback query as a message for now, or create a separate model
        TelegramMessage::create([
            'bot_id' => $bot->id,
            'user_id' => $telegramUserId,
            'type' => 'callback_query',
            'content' => $callbackData,
            'response' => $response_text,
            'processed_at' => now(),
        ]);
    }

    protected function handleNaturalLanguage(string $text): array
    {
        $intent = $this->intentDetector->detect($text);
        return $this->responseEngine->generate($intent);
    }
    
    protected function handleCommand(string $text): array
    {
        $response_text = match ($text) {
            '/start' => 'Welcome to the bot!',
            default => 'Unknown command.',
        };
        return ['text' => $response_text];
    }
}
