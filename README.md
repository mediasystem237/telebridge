# TeleBridge: Le Connecteur Telegram pour Laravel

TeleBridge est un **package Laravel léger** conçu pour connecter votre application Laravel avec l'API Telegram Bot. Il agit comme un **connecteur pur**, gérant uniquement la communication avec Telegram et laissant toute la logique métier (IA, base de connaissances, etc.) à votre application Laravel.

## ✨ Philosophie

**TeleBridge = Connecteur Uniquement**

✅ **Son rôle :**
- Recevoir les webhooks Telegram
- Router les messages vers votre backend Laravel
- Envoyer les réponses formatées à Telegram
- Gérer les claviers interactifs Telegram
- Supporter tous les types de messages (texte, photo, document, vidéo, audio, etc.)

❌ **Pas son rôle :**
- Intelligence artificielle (IA)
- Détection d'intention
- Génération de réponses
- Gestion des licences
- Base de connaissances
- Logique métier

> **Principe :** Votre application Laravel contient toute l'intelligence. TeleBridge est juste le pont entre Telegram et votre app.

---

## 🚀 Installation

### Étape 1 : Installer via Composer

```bash
composer require mbindi/telebridge
```

### Étape 2 : Publier les migrations

```bash
php artisan vendor:publish --tag=telebridge-migrations
php artisan migrate
```

### Étape 3 : Publier la configuration (optionnel)

```bash
php artisan vendor:publish --tag=telebridge-config
```

### Étape 4 : Configuration

Ajoutez vos tokens Telegram dans `.env` :

```env
TELEGRAM_BOT_TOKEN=123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
TELEGRAM_WEBHOOK_SECRET=votre_secret_pour_webhook
```

---

## 📊 Architecture

```
┌─────────────────────────────────────────────────────────┐
│                   TELEGRAM BOT API                       │
└──────────────────────┬──────────────────────────────────┘
                       │ Webhook
                       ↓
┌─────────────────────────────────────────────────────────┐
│              TELEBRIDGE (Connecteur)                     │
│  - TeleBridgeController (webhook)                       │
│  - MessageRouter (dispatch)                             │
│  - TelegramClient (API Telegram)                        │
└──────────────────────┬──────────────────────────────────┘
                       │ Dispatch Job
                       ↓
┌─────────────────────────────────────────────────────────┐
│            VOTRE APPLICATION LARAVEL                     │
│  - Votre logique métier                                 │
│  - Votre IA / Intelligence                              │
│  - Votre base de données                                │
│  - Vos services                                         │
└──────────────────────┬──────────────────────────────────┘
                       │ Retour réponse
                       ↓
┌─────────────────────────────────────────────────────────┐
│              TELEBRIDGE (Connecteur)                     │
│  - TelegramClient envoie à Telegram                     │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ↓
┌─────────────────────────────────────────────────────────┐
│                UTILISATEUR TELEGRAM                      │
└─────────────────────────────────────────────────────────┘
```

---

## 🎯 Guide de Démarrage Rapide

### 1. Créer un bot Telegram

Parlez à [@BotFather](https://t.me/BotFather) sur Telegram pour créer un bot et obtenir un token.

### 2. Créer un bot dans votre application

```php
use Mbindi\Telebridge\Models\TelegramBot;

$bot = TelegramBot::create([
    'user_id' => auth()->id(),
    'token' => env('TELEGRAM_BOT_TOKEN'),
    'name' => 'Mon Bot Assistant',
    'is_active' => true,
]);
```

### 3. Configurer le webhook

```bash
php artisan telebridge:set-webhook
```

Ou manuellement :

```php
use Mbindi\Telebridge\Services\TelegramClient;

$client = new TelegramClient();
$client->setWebhook(
    token: env('TELEGRAM_BOT_TOKEN'),
    url: route('telebridge.webhook', ['bot_token' => env('TELEGRAM_BOT_TOKEN')])
);
```

### 4. Créer un Job pour traiter les messages

**C'est ici que VOUS mettez votre logique !**

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mbindi\Telebridge\Models\TelegramBot;
use Mbindi\Telebridge\Models\TelegramMessage;
use Mbindi\Telebridge\Services\TelegramClient;

class ProcessTelegramMessage implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public TelegramBot $bot,
        public TelegramMessage $message,
        public int $chatId
    ) {}

    public function handle(TelegramClient $telegramClient)
    {
        // 1. Récupérer le contenu du message
        $userMessage = $this->message->content;

        // 2. 🔥 VOTRE LOGIQUE ICI (IA, base de connaissances, etc.)
        $response = $this->generateResponse($userMessage);

        // 3. Envoyer la réponse via TeleBridge
        $telegramClient->sendMessage(
            token: $this->bot->token,
            chatId: $this->chatId,
            text: $response,
            options: ['parse_mode' => 'Markdown']
        );

        // 4. Sauvegarder la réponse
        $this->message->markAsProcessed($response);
    }

    protected function generateResponse(string $message): string
    {
        // 🔥 VOTRE INTELLIGENCE ICI
        // Exemples :
        // - Appeler votre service IA
        // - Consulter votre base de connaissances
        // - Utiliser GPT/Claude/DeepSeek
        // - Logique métier personnalisée
        
        return "Réponse générée par votre logique !";
    }
}
```

### 5. C'est tout ! 🎉

TeleBridge gère automatiquement :
- ✅ Réception des webhooks
- ✅ Dispatch du job `ProcessTelegramMessage`
- ✅ Enregistrement des messages en base
- ✅ Gestion des utilisateurs Telegram

---

## 📚 Utilisation Avancée

### Envoyer des messages avec claviers interactifs

```php
use Mbindi\Telebridge\Services\TelegramClient;
use Mbindi\Telebridge\Services\KeyboardBuilder;

$keyboard = KeyboardBuilder::inline()
    ->row([
        KeyboardBuilder::inline()->button('Option 1', ['callback_data' => 'option_1']),
        KeyboardBuilder::inline()->button('Option 2', ['callback_data' => 'option_2']),
    ])
    ->row([
        KeyboardBuilder::inline()->button('Aide', ['callback_data' => 'help']),
    ])
    ->build();

$client = new TelegramClient();
$client->sendMessage(
    token: $bot->token,
    chatId: $chatId,
    text: 'Choisissez une option :',
    options: ['reply_markup' => $keyboard]
);
```

### Gérer les clics sur boutons (callback queries)

Les callbacks sont automatiquement gérés par TeleBridge et dispatchés comme messages de type `callback_query`. Gérez-les dans votre job :

```php
public function handle(TelegramClient $telegramClient)
{
    if ($this->message->isCallback()) {
        $action = $this->message->content; // 'option_1', 'option_2', etc.
        
        $response = match($action) {
            'option_1' => 'Vous avez choisi l\'option 1',
            'option_2' => 'Vous avez choisi l\'option 2',
            'help' => 'Voici l\'aide...',
            default => 'Action inconnue',
        };
        
        $telegramClient->sendMessage(
            token: $this->bot->token,
            chatId: $this->chatId,
            text: $response
        );
    }
}
```

### Envoyer différents types de médias

```php
// Photo
$client->sendPhoto(
    token: $bot->token,
    chatId: $chatId,
    photo: 'https://example.com/image.jpg',
    options: ['caption' => 'Ma photo']
);

// Document
$client->sendDocument(
    token: $bot->token,
    chatId: $chatId,
    document: 'file_id_or_url',
    options: ['caption' => 'Mon document']
);

// Vidéo
$client->sendVideo(
    token: $bot->token,
    chatId: $chatId,
    video: 'file_id_or_url'
);

// Localisation
$client->sendLocation(
    token: $bot->token,
    chatId: $chatId,
    latitude: 48.8566,
    longitude: 2.3522
);

// Contact
$client->sendContact(
    token: $bot->token,
    chatId: $chatId,
    phoneNumber: '+33123456789',
    firstName: 'John Doe'
);
```

### Afficher l'indicateur "en train d'écrire..."

```php
$client->sendChatAction(
    token: $bot->token,
    chatId: $chatId,
    action: 'typing' // ou 'upload_photo', 'record_video', etc.
);
```

### Éditer un message existant

```php
$client->editMessageText(
    token: $bot->token,
    chatId: $chatId,
    messageId: 123,
    text: 'Message mis à jour !'
);
```

---

## 🔧 Modèles Disponibles

### TelegramBot

```php
use Mbindi\Telebridge\Models\TelegramBot;

// Créer un bot
$bot = TelegramBot::create([
    'user_id' => $userId,
    'license_id' => $licenseId, // Optionnel
    'token' => 'your-bot-token',
    'name' => 'Mon Bot',
    'is_active' => true,
]);

// Relations
$bot->user;          // Propriétaire du bot
$bot->license;       // Licence associée (si applicable)
$bot->messages;      // Messages du bot

// Helpers
$bot->hasActiveLicense();
$bot->getRemainingMessages();
$bot->getWebhookUrl();
$bot->activate();
$bot->deactivate();
```

### TelegramMessage

```php
use Mbindi\Telebridge\Models\TelegramMessage;

// Les messages sont créés automatiquement par TeleBridge
$message = TelegramMessage::find($id);

// Relations
$message->bot;           // Bot qui a reçu le message
$message->telegramUser;  // Utilisateur Telegram
$message->conversation;  // Conversation (si vous avez ce modèle)

// Vérifications de type
$message->isText();
$message->isPhoto();
$message->isDocument();
$message->isCallback();

// Helpers
$message->isProcessed();
$message->getDecodedContent();
$message->markAsProcessed($response, $metadata);

// Scopes
TelegramMessage::unprocessed()->get();
TelegramMessage::ofType('photo')->get();
```

### TelegramUser

```php
use Mbindi\Telebridge\Models\TelegramUser;

$user = TelegramUser::where('telegram_id', $telegramId)->first();

$user->telegram_id;  // ID Telegram
$user->username;     // @username
$user->first_name;
$user->last_name;
$user->last_seen;    // Dernière activité
```

---

## 🎨 Service Provider

TeleBridge s'enregistre automatiquement via le Service Provider Laravel.

### Routes

Le package enregistre automatiquement la route webhook :

```
POST /telebridge/webhook/{bot_token}
```

Cette route est gérée par `TeleBridgeController`.

---

## 🔐 Sécurité

### Validation du webhook

TeleBridge valide automatiquement les webhooks Telegram via le middleware `VerifyTelegramSignature` (si configuré avec `TELEGRAM_WEBHOOK_SECRET`).

### Recommandations

1. **Utilisez HTTPS** : Telegram requiert HTTPS pour les webhooks
2. **Protégez votre token** : Ne le commitez jamais dans Git
3. **Utilisez des Jobs asynchrones** : Traitez les messages en queue pour éviter les timeouts
4. **Validez les entrées** : Validez toujours les données utilisateur dans votre logique

---

## 📖 Exemples d'Intégration

### Avec une IA (OpenAI, Claude, DeepSeek, etc.)

```php
protected function generateResponse(string $message): string
{
    $response = Http::post('https://api.openai.com/v1/chat/completions', [
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'user', 'content' => $message]
        ],
    ]);

    return $response->json()['choices'][0]['message']['content'];
}
```

### Avec une base de connaissances

```php
protected function generateResponse(string $message): string
{
    // Chercher dans votre base de connaissances
    $knowledge = KnowledgeBase::where('user_id', $this->bot->user_id)
        ->where('contenu', 'like', "%{$message}%")
        ->first();

    if ($knowledge) {
        return $knowledge->contenu;
    }

    return "Désolé, je n'ai pas trouvé de réponse à votre question.";
}
```

### Avec gestion de licence/quota

```php
public function handle(TelegramClient $telegramClient)
{
    // Vérifier la licence
    if (!$this->bot->hasActiveLicense()) {
        $telegramClient->sendMessage(
            $this->bot->token,
            $this->chatId,
            "❌ Votre licence a expiré."
        );
        return;
    }

    // Vérifier le quota
    if ($this->bot->getRemainingMessages() <= 0) {
        $telegramClient->sendMessage(
            $this->bot->token,
            $this->chatId,
            "⚠️ Quota de messages épuisé."
        );
        return;
    }

    // Traiter le message...
    $response = $this->generateResponse($this->message->content);

    // Envoyer la réponse...
    $telegramClient->sendMessage(/*...*/);

    // Décrémenter le quota
    $license = $this->bot->user->activeLicense();
    $license->decrement('messages_remaining');
}
```

---

## 🔧 Commandes Artisan

### Configurer le webhook

```bash
php artisan telebridge:set-webhook
```

Options :
```bash
php artisan telebridge:set-webhook {bot_token} --url=https://example.com/webhook
```

### Installer TeleBridge

```bash
php artisan telebridge:install
```

Cette commande :
- Publie la configuration
- Exécute les migrations
- Affiche les instructions de setup

---

## 🤝 Contribuer

TeleBridge est open-source ! Les contributions sont les bienvenues.

### Ligne directrice

**TeleBridge doit rester un connecteur léger.**

✅ **Contributions acceptées :**
- Nouvelles méthodes API Telegram
- Amélioration de la gestion des webhooks
- Support de nouveaux types de messages Telegram
- Optimisations de performance
- Corrections de bugs
- Amélioration de la documentation

❌ **Contributions refusées :**
- Logique métier (IA, génération de réponses, etc.)
- Systèmes de licence/quota (à implémenter dans votre app)
- Bases de connaissances
- Systèmes d'analytics
- Toute fonctionnalité qui n'est pas directement liée à la communication Telegram

---

## 📝 Licence

MIT License

---

## 🙏 Remerciements

- [Telegram Bot API](https://core.telegram.org/bots/api)
- [Laravel Framework](https://laravel.com)

---

## 📞 Support

- **Issues** : [GitHub Issues](https://github.com/mbindi/telebridge/issues)
- **Email** : support@telebridge.dev
- **Documentation** : [docs.telebridge.dev](https://docs.telebridge.dev)

---

**TeleBridge** - Le connecteur Telegram pour Laravel, simple et efficace. 🚀
