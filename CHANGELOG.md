# Changelog - TeleBridge

## Version 2.1.0 - DX & Production-Ready (24 Octobre 2025)

### ✨ Nouvelles Fonctionnalités

#### 🔗 Webhook Avancé
- `setWebhook()` supporte maintenant `allowed_updates` (filtrer les types de messages)
- `setWebhook()` supporte `certificate` (SSL self-signed)
- `setWebhook()` supporte `secret_token` (validation sécurisée)
- `setWebhook()` supporte `max_connections` et `ip_address`
- `deleteWebhook()` supporte `drop_pending_updates`

#### 🐛 Gestion d'Erreurs Avancée
- `getLastError()` - Récupère la dernière erreur API Telegram
- `getLastResponse()` - Récupère la réponse HTTP brute
- `hasError(string)` - Vérifie si une erreur spécifique s'est produite
- `resetErrors()` - Réinitialise les erreurs
- Sanitization des URLs dans les logs (tokens cachés)

#### 🧪 Commande de Test
- `php artisan telebridge:test` - Test rapide de connexion
- `php artisan telebridge:test {chat_id} {message}` - Test d'envoi
- Options `--token` et `--bot` pour spécifier le bot
- Affichage détaillé des erreurs avec conseils

#### 🎯 Interactions Avancées
- `answerInlineQuery()` - Répondre aux inline queries
- `answerPreCheckoutQuery()` - Gérer les paiements (e-commerce)
- `answerShippingQuery()` - Gérer la livraison (e-commerce)

#### 📦 Classes de Gestion de Fichiers
- `TelegramFile` - Classe pour gérer les fichiers Telegram
  - `fromTelegramResponse()` - Créer depuis réponse API
  - `getDownloadUrl()` - URL de téléchargement
  - `download()` - Télécharger vers Laravel Storage
  - `getExtension()`, `getMimeType()`, `getFormattedSize()`
- `TelegramPhoto` - Classe pour gérer les photos
  - `fromPhotoArray()` - Sélectionner taille (largest/smallest/medium)
  - `getDimensions()`, `getAspectRatio()`
  - `isPortrait()`, `isLandscape()`, `isSquare()`

#### ⚙️ Améliorations Diverses
- `getFileDownloadUrl()` - Construit l'URL de téléchargement

### 📊 Statistiques

- **+3 nouvelles méthodes API** : 25 méthodes au total
- **+7 méthodes de gestion d'erreurs**
- **+2 classes de données** : TelegramFile, TelegramPhoto
- **+1 commande Artisan**

### 🔄 Pas de Breaking Changes

Toutes les fonctionnalités v2.0 continuent de fonctionner. Les améliorations sont **additives**.

---

## Version 2.0.0 - Package Connecteur Pur (24 Octobre 2025)

### 🎯 Changements Majeurs

**TeleBridge a été transformé en connecteur pur** pour Telegram, supprimant toute logique métier et laissant votre application Laravel gérer l'intelligence.

### ✅ Ajouts

#### Nouvelles méthodes TelegramClient
- `sendAudio()` - Envoyer des fichiers audio
- `sendVoice()` - Envoyer des messages vocaux
- `sendSticker()` - Envoyer des stickers
- `sendLocation()` - Envoyer des localisations GPS
- `sendContact()` - Envoyer des contacts
- `editMessageText()` - Éditer un message existant
- `editMessageReplyMarkup()` - Éditer le clavier d'un message
- `deleteMessage()` - Supprimer un message
- `sendChatAction()` - Envoyer une action (typing, upload_photo, etc.)
- `getFile()` - Récupérer les infos d'un fichier
- `getMe()` - Récupérer les infos du bot
- `getChat()` - Récupérer les infos d'un chat
- `getChatMemberCount()` - Compter les membres d'un chat
- `getChatMember()` - Récupérer les infos d'un membre
- `deleteWebhook()` - Supprimer le webhook
- `getWebhookInfo()` - Infos sur le webhook
- `formatMarkdown()` - Formater pour Telegram Markdown
- `formatHTML()` - Formater pour Telegram HTML

#### Améliorations des modèles

**TelegramBot**
- Ajout de `license_id` (relation optionnelle)
- Méthode `hasActiveLicense()` - Vérifie si licence active
- Méthode `getRemainingMessages()` - Quota restant
- Méthode `getWebhookUrl()` - URL du webhook
- Méthode `isActive()` - Vérifie si bot actif
- Méthode `activate()` / `deactivate()` - Activer/désactiver
- Relation `license()` avec App\Models\License

**TelegramMessage**
- Ajout de `ai_metadata` (stockage métadonnées IA)
- Ajout de `intent_data` (stockage intentions détectées)
- Méthodes `isText()`, `isPhoto()`, `isDocument()`, `isAudio()`, `isVideo()`, `isCallback()`
- Méthode `getDecodedContent()` - Décode le contenu JSON
- Méthode `markAsProcessed()` - Marque comme traité
- Scopes `unprocessed()`, `processed()`, `ofType()`
- Relation `conversation()` avec App\Models\Conversation (optionnel)

#### Migrations
- `2025_10_24_120001` - Ajout `license_id` à `telegram_bots`
- `2025_10_24_120002` - Ajout `ai_metadata` et `intent_data` à `telegram_messages`

### ❌ Suppressions (Breaking Changes)

**Fichiers supprimés** (logique métier déplacée vers l'application)
- `IntentDetector.php` - Détection d'intention (à implémenter dans votre app)
- `ResponseEngine.php` - Génération de réponses (à implémenter dans votre app)
- `IntegrationManager.php` - Non nécessaire

**Changements dans MessageRouter**
- Suppression de la dépendance à `IntentDetector`
- Suppression de la dépendance à `ResponseEngine`
- Simplification : route uniquement vers votre Job Laravel
- Commandes système `/start` et `/help` gérées localement
- Tous les autres messages dispatchés vers `App\Jobs\ProcessTelegramMessage`

### 🔄 Modifications

**MessageRouter**
- **AVANT** : Gérait la logique métier (détection intention + génération réponse)
- **APRÈS** : Route simplement vers votre Job Laravel
- Détection automatique des types de messages (texte, photo, document, etc.)
- Extraction intelligente du contenu selon le type
- Gestion des callback queries séparée

**TelegramClient**
- Amélioration de la gestion d'erreurs
- Logs détaillés pour debug
- Timeout de 10s sur toutes les requêtes
- Retour `null` en cas d'erreur au lieu d'exception

### 📚 Documentation

- README complètement réécrit
- Exemples d'intégration avec IA (OpenAI, Claude, DeepSeek)
- Exemples avec base de connaissances
- Exemples avec gestion licence/quota
- Guide d'architecture claire
- Exemples de Jobs Laravel

### 🔧 Migration depuis v1.x

#### Étape 1 : Supprimer les anciennes dépendances

Les classes suivantes n'existent plus :
```php
// ❌ N'existe plus
use Mbindi\Telebridge\Services\IntentDetector;
use Mbindi\Telebridge\Services\ResponseEngine;
use Mbindi\Telebridge\Services\IntegrationManager;
```

#### Étape 2 : Créer votre Job de traitement

Créez `App\Jobs\ProcessTelegramMessage` :
```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mbindi\Telebridge\Models\TelegramBot;
use Mbindi\Telebridge\Models\TelegramMessage;
use Mbindi\Telebridge\Services\TelegramClient;

class ProcessTelegramMessage implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TelegramBot $bot,
        public TelegramMessage $message,
        public int $chatId
    ) {}

    public function handle(TelegramClient $telegramClient)
    {
        // 🔥 VOTRE LOGIQUE ICI
        $response = $this->generateResponse($this->message->content);

        $telegramClient->sendMessage(
            token: $this->bot->token,
            chatId: $this->chatId,
            text: $response
        );

        $this->message->markAsProcessed($response);
    }

    protected function generateResponse(string $message): string
    {
        // Votre IA / logique métier
        return "Votre réponse intelligente !";
    }
}
```

#### Étape 3 : Exécuter les nouvelles migrations

```bash
php artisan migrate
```

#### Étape 4 : Mettre à jour vos références

**AVANT (v1.x)** :
```php
// La logique était dans TeleBridge
$intentDetector->detect($text);
$responseEngine->generate($intent);
```

**APRÈS (v2.0)** :
```php
// La logique est dans VOTRE app
// TeleBridge dispatch juste votre Job
ProcessTelegramMessage::dispatch($bot, $message, $chatId);
```

### 🎯 Philosophie v2.0

**TeleBridge v2.0 = Connecteur Pur**

```
┌─────────────────────────────────────────┐
│         TELEGRAM BOT API                │
└──────────────┬──────────────────────────┘
               │
               ↓
┌─────────────────────────────────────────┐
│    TELEBRIDGE (Connecteur)              │
│    - Reçoit webhook                     │
│    - Route vers votre app               │
│    - Envoie réponses                    │
└──────────────┬──────────────────────────┘
               │
               ↓
┌─────────────────────────────────────────┐
│    VOTRE APPLICATION                    │
│    🔥 TOUTE L'INTELLIGENCE ICI 🔥      │
│    - IA                                 │
│    - Base de connaissances              │
│    - Logique métier                     │
└─────────────────────────────────────────┘
```

**Avantages** :
- ✅ Package léger et maintenable
- ✅ Vous gardez le contrôle total de votre logique
- ✅ Facile à tester
- ✅ Pas de dépendances inutiles
- ✅ Réutilisable pour tout projet Laravel

### 🐛 Corrections de bugs

- Meilleure gestion des erreurs API Telegram
- Gestion correcte des messages sans texte
- Support des callback queries amélioré
- Gestion des timeouts webhook (10s max)

### 🔒 Sécurité

- Validation améliorée des webhooks
- Logs sécurisés (pas de tokens dans les logs)
- Timeout sur toutes les requêtes HTTP

---

## Version 1.0.0 - Release Initiale

Version initiale avec logique métier intégrée (obsolète).

