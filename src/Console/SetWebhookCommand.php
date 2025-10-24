<?php

namespace Mbindi\Telebridge\Console;

use Illuminate\Console\Command;
use Mbindi\Telebridge\Services\TelegramClient; // Assumes this service will exist

class SetWebhookCommand extends Command
{
    protected $signature = 'telebridge:set-webhook {bot_token?} {--url=}';
    protected $description = 'Set the Telegram webhook for a specific bot';

    public function handle(TelegramClient $client)
    {
        $botToken = $this->argument('bot_token') ?? config('telebridge.bot.token');
        if (empty($botToken) || $botToken === 'your-default-bot-token-here') {
            $this->error('Bot token is not configured. Please provide it as an argument or set it in your .env file.');
            return 1;
        }

        $webhookUrl = $this->option('url');
        if (empty($webhookUrl)) {
            $webhookUrl = route('telebridge.webhook', ['bot_token' => $botToken]);
        }

        $this->info("Setting webhook to: {$webhookUrl}");

        try {
            // Once TelegramClient is implemented, this will make the actual API call.
            // $response = $client->setWebhook($botToken, $webhookUrl);

            // Mocking success for now
            $response = ['ok' => true, 'description' => 'Webhook was set'];

            if ($response['ok']) {
                $this->info('Webhook set successfully!');
                $this->line($response['description']);
            } else {
                $this->error('Failed to set webhook: ' . ($response['description'] ?? 'Unknown error.'));
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
