# 🚀 TeleBridge v2.0
## Le Connecteur Telegram pour Laravel

<div align="center">

[![Latest Version](https://img.shields.io/badge/version-2.0.0-blue.svg)](https://github.com/mbindi/telebridge)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-777BB4.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-10%20%7C%2011-FF2D20.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**Un connecteur Laravel léger et puissant pour l'API Telegram Bot**

[Installation](#-installation) • [Fonctionnalités](#-fonctionnalités) • [Documentation](#-documentation) • [Exemples](#-exemples)

</div>

---

## 🎯 Qu'est-ce que TeleBridge ?

TeleBridge est un **package Laravel minimaliste** qui fait **une seule chose et la fait bien** : connecter votre application Laravel à l'API Telegram Bot.

### 🌟 La Philosophie

```
┌─────────────────────────────────────────┐
│   TELEGRAM BOT API                       │
└──────────────┬──────────────────────────┘
               │
               ↓
┌─────────────────────────────────────────┐
│   📦 TELEBRIDGE                         │
│   • Reçoit les webhooks                 │
│   • Route vers votre app                │
│   • Envoie les réponses                 │
│   • PAS de logique métier               │
└──────────────┬──────────────────────────┘
               │
               ↓
┌─────────────────────────────────────────┐
│   🧠 VOTRE APPLICATION                  │
│   • Votre IA (GPT, Claude, etc.)        │
│   • Votre base de connaissances         │
│   • Votre logique métier                │
│   • Vous gardez le contrôle total      │
└─────────────────────────────────────────┘
```

**TeleBridge ne décide pas pour vous.** Il transporte simplement les messages entre Telegram et votre application.

---

## ✨ Fonctionnalités

### 🔌 Communication Telegram Complète

#### Méthodes de Base
- ✅ `sendMessage()` - Envoyer des messages texte (Markdown, HTML)
- ✅ `editMessageText()` - Éditer des messages existants
- ✅ `deleteMessage()` - Supprimer des messages

#### Support Média Complet
- 📷 `sendPhoto()` - Envoyer des photos
- 📄 `sendDocument()` - Envoyer des documents (PDF, ZIP, etc.)
- 🎥 `sendVideo()` - Envoyer des vidéos
- 🎵 `sendAudio()` - Envoyer des fichiers audio
- 🎤 `sendVoice()` - Envoyer des messages vocaux
- 🎨 `sendSticker()` - Envoyer des stickers

#### Partage d'Informations
- 📍 `sendLocation()` - Partager une localisation GPS
- 👤 `sendContact()` - Partager un contact

#### Interactions Avancées
- ⌨️ `KeyboardBuilder` - Créer des claviers interactifs (inline, reply)
- 🖱️ `answerCallbackQuery()` - Gérer les clics sur boutons
- 💬 `sendChatAction()` - Afficher "en train d'écrire...", "upload photo", etc.

#### Gestion Webhook
- 🔗 `setWebhook()` - Configurer le webhook
- ❌ `deleteWebhook()` - Supprimer le webhook
- ℹ️ `getWebhookInfo()` - Informations sur le webhook

#### Informations
- 🤖 `getMe()` - Informations du bot
- 💬 `getChat()` - Informations d'un chat
- 👥 `getChatMemberCount()` - Nombre de membres
- 👤 `getChatMember()` - Informations d'un membre
- 📁 `getFile()` - Récupérer un fichier

---

### 🏗️ Architecture Laravel Native

#### Modèles Eloquent
```php
// TelegramBot - Gérez vos bots
$bot = TelegramBot::create([
    'user_id' => auth()->id(),
    'token' => env('TELEGRAM_BOT_TOKEN'),
    'name' => 'Mon Bot Assistant',
]);

// TelegramMessage - Historique des messages
$messages = TelegramMessage::where('bot_id', $bot->id)
    ->unprocessed()
    ->get();

// TelegramUser - Utilisateurs Telegram
$user = TelegramUser::where('telegram_id', 123456789)->first();
```

#### Relations Laravel
```php
// Relations automatiques
$bot->user;           // Propriétaire du bot
$bot->license;        // Licence associée (optionnel)
$bot->messages;       // Tous les messages

$message->bot;        // Bot qui a reçu le message
$message->telegramUser; // Utilisateur Telegram
$message->conversation; // Conversation Laravel (optionnel)
```

#### Helpers Pratiques
```php
// TelegramBot helpers
$bot->hasActiveLicense();      // Vérifie la licence
$bot->getRemainingMessages();  // Quota restant
$bot->getWebhookUrl();         // URL du webhook
$bot->activate();              // Activer le bot
$bot->deactivate();            // Désactiver le bot

// TelegramMessage helpers
$message->isText();            // Message texte ?
$message->isPhoto();           // Photo ?
$message->isCallback();        // Clic sur bouton ?
$message->isProcessed();       // Déjà traité ?
$message->markAsProcessed($response); // Marquer comme traité
```

#### Scopes Eloquent
```php
// Messages non traités
TelegramMessage::unprocessed()->get();

// Messages traités
TelegramMessage::processed()->get();

// Par type
TelegramMessage::ofType('photo')->get();
TelegramMessage::ofType('document')->get();
```

---

### 🎨 Claviers Interactifs

#### Inline Keyboards (Boutons sous le message)
```php
use Mbindi\Telebridge\Services\KeyboardBuilder;

$keyboard = KeyboardBuilder::inline()
    ->row([
        KeyboardBuilder::inline()->button('✅ Oui', ['callback_data' => 'yes']),
        KeyboardBuilder::inline()->button('❌ Non', ['callback_data' => 'no']),
    ])
    ->row([
        KeyboardBuilder::inline()->button('ℹ️ Plus d\'infos', ['url' => 'https://example.com']),
    ])
    ->build();

$client->sendMessage($token, $chatId, 'Êtes-vous d\'accord ?', [
    'reply_markup' => $keyboard
]);
```

#### Reply Keyboards (Clavier personnalisé)
```php
$keyboard = KeyboardBuilder::reply()
    ->row(['🏠 Accueil', '📞 Contact'])
    ->row(['📚 Catalogue', '🛒 Panier'])
    ->resize()
    ->oneTime()
    ->build();
```

#### Gestion des Callbacks
```php
// Les callbacks sont automatiquement gérés
// Votre Job reçoit le callback_data
public function handle(TelegramClient $client)
{
    if ($this->message->isCallback()) {
        $action = $this->message->content; // 'yes', 'no', etc.
        
        $response = match($action) {
            'yes' => 'Super ! On continue.',
            'no' => 'D\'accord, on arrête là.',
            default => 'Action inconnue',
        };
        
        $client->sendMessage($this->bot->token, $this->chatId, $response);
    }
}
```

---

### 🔄 Jobs Asynchrones (Queues)

TeleBridge dispatch automatiquement vos messages dans des **Laravel Jobs** pour un traitement asynchrone :

```php
// TeleBridge reçoit le message et dispatch automatiquement
ProcessTelegramMessage::dispatch($bot, $message, $chatId);

// Votre Job traite le message sans bloquer Telegram
class ProcessTelegramMessage implements ShouldQueue
{
    use Queueable;
    
    public function handle(TelegramClient $client)
    {
        // Traitement long possible (IA, base de données, etc.)
        $response = $this->generateResponse($this->message->content);
        
        // Envoi de la réponse
        $client->sendMessage($this->bot->token, $this->chatId, $response);
    }
}
```

**Avantages :**
- ✅ Pas de timeout webhook (Telegram requiert réponse < 10s)
- ✅ Traitement parallèle de plusieurs messages
- ✅ Retry automatique en cas d'erreur
- ✅ Monitoring via Laravel Horizon

---

### 🔒 Sécurité Intégrée

#### Validation des Webhooks
```php
// Middleware automatique pour vérifier les webhooks Telegram
protected $middleware = [
    VerifyTelegramSignature::class
];
```

#### Gestion des Erreurs
```php
// Toutes les requêtes API ont un timeout de 10s
// Logs automatiques en cas d'erreur
// Retour null en cas d'échec (pas d'exception)

$result = $client->sendMessage($token, $chatId, $text);
if ($result === null) {
    // Erreur gérée, consultez les logs
}
```

#### Protection Token
- ✅ Tokens jamais loggés
- ✅ Configuration via .env
- ✅ Validation automatique

---

### 📦 Métadonnées et Analytics

#### Stockage des Métadonnées IA
```php
// Stocker les infos de votre IA
$message->update([
    'ai_metadata' => [
        'model' => 'gpt-4',
        'tokens' => 150,
        'cost' => 0.002,
        'processing_time' => 1.5,
    ],
    'intent_data' => [
        'primary_intent' => 'ask_price',
        'confidence' => 0.95,
        'entities' => ['product' => 'smartphone'],
    ]
]);
```

#### Requêtes Optimisées
```php
// Statistiques par bot
$stats = TelegramMessage::where('bot_id', $bot->id)
    ->selectRaw('
        COUNT(*) as total,
        COUNT(CASE WHEN processed_at IS NOT NULL THEN 1 END) as processed,
        AVG(JSON_EXTRACT(ai_metadata, "$.processing_time")) as avg_time
    ')
    ->first();

// Messages par type
$distribution = TelegramMessage::where('bot_id', $bot->id)
    ->groupBy('type')
    ->selectRaw('type, COUNT(*) as count')
    ->get();
```

---

### 🛠️ Commandes Artisan

#### Installation
```bash
php artisan telebridge:install
```
- ✅ Publie les migrations
- ✅ Publie la configuration
- ✅ Affiche les instructions de setup

#### Configuration Webhook
```bash
php artisan telebridge:set-webhook
```
- ✅ Configure automatiquement le webhook
- ✅ Utilise l'URL de votre application
- ✅ Teste la connexion

---

## 📥 Installation

### Étape 1 : Composer
```bash
composer require mbindi/telebridge
```

### Étape 2 : Migrations
```bash
php artisan vendor:publish --tag=telebridge-migrations
php artisan migrate
```

### Étape 3 : Configuration
```env
TELEGRAM_BOT_TOKEN=123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
TELEGRAM_WEBHOOK_SECRET=votre_secret_webhook
```

### Étape 4 : Webhook
```bash
php artisan telebridge:set-webhook
```

---

## 💡 Exemples d'Utilisation

### 1️⃣ Bot Simple avec Réponses Prédéfinies

```php
<?php

namespace App\Jobs;

use Mbindi\Telebridge\Services\TelegramClient;

class ProcessTelegramMessage
{
    public function handle(TelegramClient $client)
    {
        $text = strtolower($this->message->content);
        
        $response = match(true) {
            str_contains($text, 'prix') => "Nos prix débutent à 100€",
            str_contains($text, 'horaire') => "Ouvert du lundi au vendredi, 9h-18h",
            str_contains($text, 'contact') => "Appelez-nous au +33 1 23 45 67 89",
            default => "Je n'ai pas compris. Tapez 'aide' pour plus d'infos."
        };
        
        $client->sendMessage($this->bot->token, $this->chatId, $response);
    }
}
```

### 2️⃣ Bot avec Intelligence Artificielle

```php
public function handle(TelegramClient $client)
{
    // Appeler votre service IA
    $aiResponse = Http::post('https://api.openai.com/v1/chat/completions', [
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'system', 'content' => 'Tu es un assistant serviable.'],
            ['role' => 'user', 'content' => $this->message->content]
        ],
    ])->json();
    
    $response = $aiResponse['choices'][0]['message']['content'];
    
    // Envoyer la réponse
    $client->sendMessage($this->bot->token, $this->chatId, $response);
    
    // Sauvegarder les métadonnées
    $this->message->update([
        'response' => $response,
        'ai_metadata' => [
            'model' => 'gpt-4',
            'tokens' => $aiResponse['usage']['total_tokens'],
            'cost' => $this->calculateCost($aiResponse['usage'])
        ],
        'processed_at' => now()
    ]);
}
```

### 3️⃣ Bot avec Base de Connaissances

```php
public function handle(TelegramClient $client)
{
    // Chercher dans votre base de connaissances
    $knowledge = KnowledgeBase::where('user_id', $this->bot->user_id)
        ->where('contenu', 'like', "%{$this->message->content}%")
        ->first();
    
    if ($knowledge) {
        $response = $knowledge->contenu;
    } else {
        $response = "Désolé, je n'ai pas trouvé de réponse à cette question.";
    }
    
    $client->sendMessage($this->bot->token, $this->chatId, $response);
}
```

### 4️⃣ Bot avec Gestion de Licence

```php
public function handle(TelegramClient $client)
{
    // Vérifier la licence
    if (!$this->bot->hasActiveLicense()) {
        $client->sendMessage(
            $this->bot->token,
            $this->chatId,
            "❌ Votre licence a expiré. Veuillez la renouveler."
        );
        return;
    }
    
    // Vérifier le quota
    if ($this->bot->getRemainingMessages() <= 0) {
        $client->sendMessage(
            $this->bot->token,
            $this->chatId,
            "⚠️ Quota de messages épuisé."
        );
        return;
    }
    
    // Traiter le message...
    $response = $this->processMessage($this->message->content);
    
    // Envoyer la réponse
    $client->sendMessage($this->bot->token, $this->chatId, $response);
    
    // Décrémenter le quota
    $license = $this->bot->user->activeLicense();
    $license->decrement('messages_remaining');
}
```

### 5️⃣ Bot avec Claviers Interactifs

```php
public function handle(TelegramClient $client)
{
    // Message de bienvenue avec options
    if ($this->message->content === '/start') {
        $keyboard = KeyboardBuilder::inline()
            ->row([
                KeyboardBuilder::inline()->button('📚 Voir le catalogue', ['callback_data' => 'catalog']),
            ])
            ->row([
                KeyboardBuilder::inline()->button('💬 Contacter le support', ['callback_data' => 'support']),
                KeyboardBuilder::inline()->button('ℹ️ À propos', ['callback_data' => 'about']),
            ])
            ->build();
        
        $client->sendMessage(
            $this->bot->token,
            $this->chatId,
            "👋 Bienvenue ! Que souhaitez-vous faire ?",
            ['reply_markup' => $keyboard]
        );
        return;
    }
    
    // Gérer les callbacks
    if ($this->message->isCallback()) {
        $action = $this->message->content;
        
        $response = match($action) {
            'catalog' => "📚 Voici notre catalogue : [lien]",
            'support' => "💬 Notre support est disponible 24/7",
            'about' => "ℹ️ Nous sommes une entreprise...",
            default => "Action inconnue"
        };
        
        $client->sendMessage($this->bot->token, $this->chatId, $response);
    }
}
```

### 6️⃣ Bot avec Support Multimédia

```php
public function handle(TelegramClient $client)
{
    // Détecter le type de message
    if ($this->message->isPhoto()) {
        $client->sendMessage(
            $this->bot->token,
            $this->chatId,
            "📷 Belle photo ! Je l'ai bien reçue."
        );
    }
    
    if ($this->message->isDocument()) {
        $client->sendMessage(
            $this->bot->token,
            $this->chatId,
            "📄 Document reçu ! Je l'analyse..."
        );
        
        // Analyser le document...
    }
    
    // Envoyer une photo en réponse
    $client->sendPhoto(
        $this->bot->token,
        $this->chatId,
        'https://example.com/image.jpg',
        ['caption' => 'Voici notre produit phare !']
    );
}
```

---

## 🎯 Cas d'Usage

### ✅ Parfait Pour

- 🤖 **Bots de Support Client** - Répondre aux questions FAQ
- 🛒 **Bots E-commerce** - Catalogue de produits, commandes
- 📰 **Bots de Notification** - Alertes, news, mises à jour
- 🎓 **Bots Éducatifs** - Quiz, cours, exercices
- 📊 **Bots d'Analytics** - Statistiques, rapports
- 🎮 **Bots de Jeu** - Interactions ludiques
- 🏢 **Bots Internes** - Outils d'équipe, automatisation

### ❌ Pas Conçu Pour

- Applications où vous voulez une IA intégrée (utilisez plutôt un service d'IA)
- Solutions clé en main (TeleBridge est un connecteur, pas une solution complète)

---

## 📊 Comparaison

| Fonctionnalité | TeleBridge v2.0 | Autres Packages |
|----------------|-----------------|-----------------|
| **Logique métier** | ❌ Aucune (à vous) | ✅ Incluse (rigide) |
| **Flexibilité** | ✅✅✅ Totale | ⚠️ Limitée |
| **Taille** | ✅ ~1750 lignes | ⚠️ 5000+ lignes |
| **Courbe d'apprentissage** | ✅ Simple | ⚠️ Complexe |
| **Maintenance** | ✅ Facile | ⚠️ Difficile |
| **API Telegram** | ✅ 22 méthodes | ⚠️ 10-15 méthodes |
| **Relations Laravel** | ✅ Complètes | ⚠️ Basiques |
| **Jobs asynchrones** | ✅ Natif | ⚠️ Optionnel |
| **Documentation** | ✅ Complète | ⚠️ Basique |

---

## 🏆 Avantages

### Pour les Développeurs
✅ **Contrôle Total** - Toute la logique dans votre app  
✅ **Simple** - Juste un connecteur, pas de complexité  
✅ **Flexible** - Utilisez l'IA de votre choix  
✅ **Testable** - Isolé et facile à tester  
✅ **Standard** - Suit les conventions Laravel  

### Pour les Projets
✅ **Léger** - ~1750 lignes de code seulement  
✅ **Rapide** - Jobs asynchrones natifs  
✅ **Scalable** - Gère facilement des milliers de messages  
✅ **Maintenable** - Code propre et bien documenté  
✅ **Production-ready** - Gestion d'erreurs robuste  

### Pour les Équipes
✅ **Onboarding Rapide** - Documentation complète  
✅ **Pas de Vendor Lock-in** - Vous gardez vos données  
✅ **Open Source** - MIT License  
✅ **Communauté** - Support et contributions  

---

## 📚 Documentation

- 📖 [README Complet](README.md) - Guide utilisateur détaillé
- 📝 [CHANGELOG](CHANGELOG.md) - Historique des versions
- 🤝 [CONTRIBUTING](CONTRIBUTING.md) - Guide de contribution
- 📦 [Packagist](https://packagist.org/packages/mbindi/telebridge) - Installation Composer

---

## 🤝 Support

- 💬 **Issues** : [GitHub Issues](https://github.com/mbindi/telebridge/issues)
- 💡 **Discussions** : [GitHub Discussions](https://github.com/mbindi/telebridge/discussions)
- 📧 **Email** : contact@mbindi.com

---

## 📜 License

TeleBridge est un logiciel open-source sous licence [MIT](LICENSE).

---

## 🎉 Conclusion

**TeleBridge fait UNE chose et la fait bien** : connecter Telegram à votre application Laravel.

Pas de logique cachée, pas de décisions prises pour vous, juste un pont solide et fiable entre Telegram et votre code.

**Vous gardez le contrôle, nous gérons la communication.** 🚀

---

<div align="center">

**Créé avec ❤️ par [Mbindi](https://github.com/mbindi)**

⭐ Si vous aimez ce projet, n'oubliez pas de lui donner une étoile sur GitHub !

[Installation](#-installation) • [Documentation](#-documentation) • [GitHub](https://github.com/mbindi/telebridge)

</div>

