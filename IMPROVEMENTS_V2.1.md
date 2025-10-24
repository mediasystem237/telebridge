# 🚀 TeleBridge v2.1 - Améliorations DX & Production-Ready

## 📋 Vue d'Ensemble

Ces améliorations respectent la philosophie de TeleBridge (connecteur pur) tout en augmentant significativement l'expérience développeur (DX) et la robustesse en production.

---

## ✨ Nouvelles Fonctionnalités

### 1. 🔗 Gestion Avancée des Webhooks

#### `setWebhook()` avec Options Avancées

```php
$client->setWebhook($token, $url, [
    // 🎯 Filtrer les types de mises à jour reçues
    'allowed_updates' => ['message', 'callback_query'],
    
    // 🔒 Certificat SSL pour self-signed
    'certificate' => $certificateContent,
    
    // 🌐 IP fixe du serveur (optionnel)
    'ip_address' => '1.2.3.4',
    
    // ⚡ Nombre max de connexions (1-100, défaut: 40)
    'max_connections' => 50,
    
    // 🗑️ Supprimer les mises à jour en attente
    'drop_pending_updates' => true,
    
    // 🔐 Token secret pour validation
    'secret_token' => 'mon_secret_123',
]);
```

**Avantages** :
- ✅ **Réduit la charge** : Recevez uniquement les types de messages nécessaires
- ✅ **Augmente la sécurité** : Ignore les données non pertinentes
- ✅ **Production-ready** : Support certificat SSL pour déploiements avancés

#### `deleteWebhook()` Amélioré

```php
// Supprimer le webhook ET les updates en attente
$client->deleteWebhook($token, dropPendingUpdates: true);
```

---

### 2. 🐛 Système de Gestion d'Erreurs Avancé

#### Accès à la Dernière Erreur

```php
$result = $client->sendMessage($token, $chatId, $text);

if ($result === null) {
    // Récupérer les détails de l'erreur
    $error = $client->getLastError();
    
    /*
    [
        'error_code' => 400,
        'description' => 'Bad Request: chat not found',
        'parameters' => []
    ]
    */
    
    // Réagir spécifiquement
    if ($error['error_code'] === 403) {
        // Utilisateur a bloqué le bot
        $this->markUserAsBlocked($chatId);
    }
}
```

#### Vérification d'Erreurs Spécifiques

```php
$client->sendMessage($token, $chatId, $text);

if ($client->hasError('chat not found')) {
    // Chat supprimé ou bot bloqué
}

if ($client->hasError('blocked by user')) {
    // Utilisateur a bloqué le bot
}
```

#### Accès à la Réponse HTTP Brute

```php
$client->sendMessage($token, $chatId, $text);

$response = $client->getLastResponse();
/*
[
    'status' => 400,
    'body' => '{"ok":false,"error_code":400,...}',
    'headers' => [...]
]
*/
```

**Avantages** :
- ✅ **Débogage facile** : Accès complet aux erreurs Telegram
- ✅ **Gestion d'erreurs fine** : Réagir spécifiquement à chaque type d'erreur
- ✅ **Pas de dépendance aux logs** : Gestion programmatique des erreurs

---

### 3. 🧪 Commande de Test Rapide

```bash
# Test de connexion basique
php artisan telebridge:test

# Test d'envoi de message
php artisan telebridge:test 123456789 "Message de test"

# Avec options
php artisan telebridge:test 123456789 "Hello" --token=123456:ABC-DEF
php artisan telebridge:test 123456789 "Hello" --bot=1
```

**Sortie** :
```
🔍 Test de connexion TeleBridge → Telegram API

📡 Test 1/3 : Récupération des informations du bot...
✅ Bot connecté :
   Nom: Mon Bot Assistant
   Username: @mon_bot
   ID: 123456789

📡 Test 2/3 : Vérification du webhook...
✅ Webhook :
   URL: https://example.com/telebridge/webhook/123456:ABC-DEF
   Updates en attente: 0

📡 Test 3/3 : Envoi d'un message de test...
✅ Message envoyé avec succès !
   Message ID: 456
   Chat ID: 123456789

🎉 Tous les tests sont passés avec succès !
```

**Avantages** :
- ✅ **Test rapide** : Vérifie la connexion sans configurer webhook
- ✅ **Débogage** : Identifie rapidement les problèmes
- ✅ **Pas de Job nécessaire** : Test direct de l'API

---

### 4. 🎯 Support Interactions Avancées

#### Inline Queries

```php
// Répondre à une inline query
$results = [
    [
        'type' => 'article',
        'id' => '1',
        'title' => 'Résultat 1',
        'input_message_content' => [
            'message_text' => 'Contenu du résultat 1'
        ]
    ],
    // ... jusqu'à 50 résultats
];

$client->answerInlineQuery($token, $inlineQueryId, $results, [
    'cache_time' => 300,
    'is_personal' => true,
]);
```

#### Pre-Checkout Queries (E-commerce)

```php
// Valider un paiement
$client->answerPreCheckoutQuery($token, $queryId, ok: true);

// Refuser un paiement
$client->answerPreCheckoutQuery(
    $token, 
    $queryId, 
    ok: false, 
    errorMessage: 'Stock insuffisant'
);
```

#### Shipping Queries (E-commerce)

```php
$client->answerShippingQuery($token, $queryId, ok: true, [
    'shipping_options' => [
        [
            'id' => 'standard',
            'title' => 'Livraison Standard',
            'prices' => [
                ['label' => 'Standard', 'amount' => 500] // 5.00€
            ]
        ]
    ]
]);
```

**Avantages** :
- ✅ **Bots inline** : Créez des bots utilisables depuis n'importe quel chat
- ✅ **E-commerce** : Gérez les paiements Telegram nativement
- ✅ **Production-ready** : Tous les types d'interactions Telegram

---

### 5. 📦 Classes de Gestion de Fichiers

#### TelegramFile

```php
use Mbindi\Telebridge\Data\TelegramFile;

// Récupérer un fichier
$response = $client->getFile($token, $fileId);
$file = TelegramFile::fromTelegramResponse($response, $token);

// URL de téléchargement
$url = $file->getDownloadUrl();
// https://api.telegram.org/file/bot<token>/<file_path>

// Télécharger localement (Laravel Storage)
$path = $file->download(); // storage/app/telegram/2025/01/24/file_unique_id.ext
$path = $file->download('s3'); // Sur S3
$path = $file->download('public', 'documents/file.pdf'); // Chemin personnalisé

// Informations
echo $file->getExtension();      // "pdf"
echo $file->getMimeType();       // "application/pdf"
echo $file->getFormattedSize();  // "2.5 MB"

// Convertir en array
$data = $file->toArray();
/*
[
    'file_id' => '...',
    'file_unique_id' => '...',
    'file_size' => 2621440,
    'file_path' => 'documents/file_123.pdf',
    'download_url' => 'https://...',
    'extension' => 'pdf',
    'mime_type' => 'application/pdf',
    'formatted_size' => '2.5 MB'
]
*/
```

#### TelegramPhoto

```php
use Mbindi\Telebridge\Data\TelegramPhoto;

// Telegram envoie toujours plusieurs tailles
$photos = $messageData['photo']; // Array de photos

// Récupérer la plus grande
$photo = TelegramPhoto::fromPhotoArray($photos, $token, 'largest');

// Récupérer la plus petite (thumbnail)
$thumb = TelegramPhoto::fromPhotoArray($photos, $token, 'smallest');

// Informations
echo $photo->getDimensions();    // "1920x1080"
echo $photo->getAspectRatio();   // 1.77
$photo->isPortrait();            // false
$photo->isLandscape();           // true
$photo->isSquare();              // false

// Télécharger
$path = $photo->download();

// Convertir en array
$data = $photo->toArray();
/*
[
    ... (propriétés TelegramFile) ...
    'width' => 1920,
    'height' => 1080,
    'dimensions' => '1920x1080',
    'aspect_ratio' => 1.77,
    'orientation' => 'landscape'
]
*/
```

**Avantages** :
- ✅ **API simple** : Pas de manipulation manuelle des réponses Telegram
- ✅ **Téléchargement facile** : En une ligne vers Laravel Storage
- ✅ **Informations riches** : Extension, MIME type, taille formatée
- ✅ **Photos intelligentes** : Choix automatique de la taille appropriée

---

## 📊 Comparaison v2.0 vs v2.1

| Fonctionnalité | v2.0 | v2.1 |
|----------------|------|------|
| **Méthodes API** | 22 | 25 (+3) |
| **Gestion erreurs** | Logs uniquement | `getLastError()` + logs |
| **Webhook options** | Basique | Avancé (filtres, certificat) |
| **Test connexion** | Manuel | Commande `telebridge:test` |
| **Inline queries** | ❌ | ✅ |
| **E-commerce** | ❌ | ✅ (pre-checkout, shipping) |
| **Gestion fichiers** | Manuel | Classes `TelegramFile` |
| **Photos** | Manuel | Classe `TelegramPhoto` |

---

## 🎯 Cas d'Usage

### Exemple 1 : Gestion d'Erreurs Fine

```php
public function handle(TelegramClient $client)
{
    $result = $client->sendMessage($this->bot->token, $this->chatId, $message);
    
    if ($result === null) {
        $error = $client->getLastError();
        
        // Réagir selon le type d'erreur
        match($error['error_code']) {
            403 => $this->handleUserBlocked(),
            400 => $this->handleInvalidRequest(),
            429 => $this->handleRateLimit(),
            default => $this->handleGenericError($error),
        };
    }
}
```

### Exemple 2 : Webhook Optimisé

```php
// Ne recevoir QUE les messages et callbacks (pas de inline queries, etc.)
$client->setWebhook($token, $url, [
    'allowed_updates' => ['message', 'callback_query'],
    'max_connections' => 100, // Pour haute charge
    'secret_token' => config('telebridge.webhook_secret'),
]);
```

### Exemple 3 : Bot Inline

```php
// Dans votre MessageRouter, détecter inline_query
if (isset($updateData['inline_query'])) {
    $this->handleInlineQuery($bot, $updateData['inline_query']);
}

protected function handleInlineQuery($bot, $inlineQueryData)
{
    $query = $inlineQueryData['query'];
    
    // Chercher dans votre base de données
    $results = Product::where('name', 'like', "%{$query}%")
        ->take(10)
        ->get()
        ->map(fn($product) => [
            'type' => 'article',
            'id' => (string) $product->id,
            'title' => $product->name,
            'description' => $product->description,
            'input_message_content' => [
                'message_text' => "*{$product->name}*\n\n{$product->description}\n\nPrix: {$product->price}€",
                'parse_mode' => 'Markdown'
            ]
        ])->toArray();
    
    $this->telegramClient->answerInlineQuery(
        $bot->token,
        $inlineQueryData['id'],
        $results
    );
}
```

### Exemple 4 : Téléchargement de Fichiers

```php
public function handle(TelegramClient $client)
{
    if ($this->message->isDocument()) {
        // Extraire le file_id du message
        $content = json_decode($this->message->content, true);
        $fileId = $content['file_id'];
        
        // Récupérer les infos du fichier
        $response = $client->getFile($this->bot->token, $fileId);
        $file = TelegramFile::fromTelegramResponse($response, $this->bot->token);
        
        // Télécharger dans Storage
        $path = $file->download('public', 'documents/' . $file->fileUniqueId . '.' . $file->getExtension());
        
        // Sauvegarder dans la base
        Document::create([
            'user_id' => $this->bot->user_id,
            'file_path' => $path,
            'file_size' => $file->fileSize,
            'mime_type' => $file->getMimeType(),
        ]);
        
        $client->sendMessage(
            $this->bot->token,
            $this->chatId,
            "✅ Document reçu et sauvegardé !\nTaille: {$file->getFormattedSize()}"
        );
    }
}
```

---

## 🔄 Migration v2.0 → v2.1

### Pas de Breaking Changes ! ✅

Toutes les fonctionnalités v2.0 continuent de fonctionner. Les améliorations sont **additives**.

### Nouvelles Méthodes Disponibles

```php
// Gestion d'erreurs
$client->getLastError();
$client->getLastResponse();
$client->hasError('description');
$client->resetErrors();

// Interactions avancées
$client->answerInlineQuery($token, $id, $results);
$client->answerPreCheckoutQuery($token, $id, $ok);
$client->answerShippingQuery($token, $id, $ok);

// Fichiers
$client->getFileDownloadUrl($token, $filePath);
TelegramFile::fromTelegramResponse($response, $token);
TelegramPhoto::fromPhotoArray($photos, $token);

// Commande artisan
php artisan telebridge:test [chat_id] [message]
```

---

## 📈 Bénéfices

### Pour les Développeurs
✅ **Meilleur DX** : Gestion d'erreurs claire et commande de test  
✅ **Moins de code** : Classes TelegramFile simplifient la gestion  
✅ **Débogage facile** : `getLastError()` au lieu de chercher dans les logs  

### Pour les Projets
✅ **Production-ready** : Certificat SSL, filtres webhook, gestion d'erreurs  
✅ **Performance** : Filtrer les updates réduit la charge  
✅ **E-commerce** : Support paiements Telegram natifs  

### Pour la Communauté
✅ **API complète** : Support de toutes les interactions Telegram  
✅ **Maintenable** : Code propre avec classes dédiées  
✅ **Documenté** : Exemples clairs pour chaque fonctionnalité  

---

## 🎉 Conclusion

**TeleBridge v2.1** reste fidèle à sa philosophie de **connecteur pur** tout en devenant significativement plus robuste et agréable à utiliser.

Chaque amélioration respecte la règle d'or :
> **"Si ça n'est pas directement lié à la communication avec Telegram, ça n'est pas dans TeleBridge."**

**Version** : 2.1.0  
**Status** : ✅ Implémenté  
**Breaking Changes** : ❌ Aucun

