<?php

namespace Mbindi\Telebridge\Services;

use Illuminate\Support\Facades\Log;
use Mbindi\Telebridge\Models\TelegramMessage;

class MessageRouter
{
    public function __construct(
        protected IntentDetector $intentDetector,
        protected ResponseEngine $responseEngine,
        protected TelegramClient $telegramClient
    ) {}

    public function route(TelegramMessage $message): void
    {
        Log::info("Routing message ID: {$message->id}");

        $response_text = str_starts_with($message->content, '/')
            ? $this->handleCommand($message)
            : $this->handleNaturalLanguage($message);
        
        if ($response_text) {
            $this->telegramClient->sendMessage(
                $message->bot->token, 
                $message->user->telegram_id, 
                $response_text
            );
        }
        
        $message->response = $response_text;
        $message->processed_at = now();
        $message->save();
    }

    protected function handleNaturalLanguage(TelegramMessage $message): string
    {
        $intent = $this->intentDetector->detect($message->content);
        return $this->responseEngine->generate($intent);
    }

    protected function handleCommand(TelegramMessage $message): string
    {
        return match ($message->content) {
            '/start' => 'Welcome to the bot!',
            default => 'Unknown command.',
        };
    }
}
