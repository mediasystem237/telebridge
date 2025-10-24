# TeleBridge

A Laravel package to manage Telegram bots with intelligence and extensibility.

## Installation

1.  Install the package via composer:
    ```bash
    composer require mbindi/telebridge
    ```

2.  Publish the configuration file and run migrations:
    ```bash
    php artisan telebridge:install
    ```

## Configuration

Set your Telegram Bot Token and other settings in your `.env` file:

```
TELEGRAM_BOT_TOKEN=your-bot-token
TELEGRAM_WEBHOOK_SECRET=your-secret-token
```

## Usage

### Set Webhook

To set your bot's webhook, run the following command. It will automatically use your app's URL and the bot token from your `.env` file.

```bash
php artisan telebridge:set-webhook
```

You can also specify a bot token and URL directly:
```bash
php artisan telebridge:set-webhook your-other-bot-token --url=https://example.com/webhook
```

### Sending Messages

You can send messages using the `TeleBridge` facade. Note that you need to provide the bot token for the client.

```php
use Mbindi\Telebridge\Facades\TeleBridge;

$botToken = config('telebridge.bot.token');
TeleBridge::sendMessage($botToken, $chatId, 'Hello from TeleBridge!');
```

## Development

This package provides a foundational structure. Key areas for expansion include:

-   Implementing the `IntentDetector` with Regex and AI drivers.
-   Creating database migrations for the models.
-   Building out the `MessageRouter` to handle various message types (photos, callbacks, etc.).
-   Implementing the `IntegrationManager` to connect with external services.
