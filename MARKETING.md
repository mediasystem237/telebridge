# 🚀 TeleBridge - Le Connecteur Telegram pour Laravel

<div align="center">

![Version](https://img.shields.io/badge/version-2.2.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4)
![Laravel](https://img.shields.io/badge/Laravel-10%20%7C%2011-FF2D20)
![License](https://img.shields.io/badge/license-MIT-green)

**Le package qui connecte Telegram à votre application Laravel**  
**Sans jamais toucher à votre logique métier**

[Installation](#-installation-en-1-minute) • [Fonctionnalités](#-fonctionnalités) • [Exemples](#-exemples) • [Documentation](#-documentation)

</div>

---

## 🎯 Pourquoi TeleBridge ?

### Le Problème

Vous voulez un bot Telegram pour votre application Laravel, mais :

❌ Les frameworks imposent leur structure (Handlers, Kernel, etc.)  
❌ Les SDK sont lourds et complexes  
❌ Vous perdez le contrôle de votre logique  
❌ Difficile d'intégrer votre propre IA  
❌ Configuration complexe  

### La Solution : TeleBridge

✅ **Connecteur pur** : Juste la communication Telegram  
✅ **Laravel natif** : Jobs, Notifications, Eloquent  
✅ **Contrôle total** : Votre logique, votre IA, vos règles  
✅ **Simple** : Pas de nouvelle syntaxe à apprendre  
✅ **Complet** : Tous les outils modernes inclus  

---

## ⚡ Installation en 1 Minute

```bash
# 1. Installer
composer require mbindi/telebridge

# 2. Setup
php artisan telebridge:install

# 3. Configurer
TELEGRAM_BOT_TOKEN=your-token-here

# 4. Développer !
php artisan telebridge:polling
```

**C'est tout ! 🎉**

---

## ✨ Fonctionnalités

### 📱 **Long Polling** (Développement Local)

```bash
php artisan telebridge:polling
```

**Plus besoin de** ngrok, webhook, ou serveur public !  
Testez votre bot **immédiatement** en local.

---

### 📢 **Notifications Laravel**

```php
// Dans votre notification
public function via($notifiable) {
    return ['telegram'];
}

// Envoyer
$user->notify(new OrderShipped($order));
```

**Intégration native** avec le système de notifications Laravel !

---

### 🤖 **Multi-Bots**

```php
TeleBridge::bot('support')->sendMessage(...);
TeleBridge::bot('marketing')->sendMessage(...);
TeleBridge::bot('alerts')->sendMessage(...);
```

**Configuration simple** dans `config/telebridge.php`

---

### 📦 **DTOs Typés**

```php
$botInfo = BotInfo::fromResponse($response);
echo $botInfo->getFullName();  // Autocomplete IDE !

$webhookInfo = WebhookInfo::fromResponse($response);
if ($webhookInfo->hasError()) {
    // Gérer l'erreur
}
```

**Finies les manipulations** `$response['result']['first_name']` !

---

### 📁 **Téléchargement Fichiers en 1 Ligne**

```php
$file = TelegramFile::fromTelegramResponse($response, $token);
$path = $file->download(); // C'est tout !

echo $file->getFormattedSize(); // "2.5 MB"
echo $file->getMimeType();      // "application/pdf"
```

---

### 🐛 **Gestion d'Erreurs Intelligente**

```php
if ($client->sendMessage(...) === null) {
    $error = TeleBridge::getLastError();
    
    if ($error['error_code'] === 403) {
        // Utilisateur a bloqué le bot
    }
}
```

**Pas d'exceptions**, juste des retours `null` et des erreurs accessibles.

---

### ⌨️ **Claviers Interactifs**

```php
$keyboard = KeyboardBuilder::inline()
    ->row([
        KeyboardBuilder::inline()->button('✅ Oui', ['callback_data' => 'yes']),
        KeyboardBuilder::inline()->button('❌ Non', ['callback_data' => 'no']),
    ])
    ->build();

TeleBridge::sendMessage($token, $chatId, "D'accord ?", [
    'reply_markup' => $keyboard
]);
```

---

## 🆚 TeleBridge vs Autres Packages

| Feature | TeleBridge | telebot-laravel | telegram-bot-sdk |
|---------|-----------|-----------------|------------------|
| **Simplicité** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐ |
| **Flexibilité** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐ |
| **Long Polling** | ✅ | ✅ | ✅ |
| **Notifications** | ✅ | ❌ | ⚠️ |
| **DTOs** | ✅ | ⚠️ | ❌ |
| **Multi-bots** | ✅ | ✅ | ✅ |
| **Gestion fichiers** | ✅ | ⚠️ | ⚠️ |
| **Docs** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐ |
| **Taille** | 2000 lignes | 5000 lignes | 10000 lignes |

**TeleBridge = Le meilleur rapport simplicité/fonctionnalités !**

---

## 💼 Cas d'Usage

### ✅ Bot Support Client
```php
// Utilise votre IA préférée
$response = $yourAI->chat($message);
TeleBridge::sendMessage($token, $chatId, $response);
```

### ✅ Notifications Automatiques
```php
// N'importe où dans votre app
$user->notify(new PaymentReceived($payment));
// → Envoyé sur Telegram automatiquement
```

### ✅ Bot E-commerce
```php
// Catalogue inline
$client->answerInlineQuery($token, $queryId, $products);

// Paiement
$client->answerPreCheckoutQuery($token, $queryId, ok: true);
```

### ✅ Alertes Système
```php
// Multi-bots pour organisation
TeleBridge::bot('alerts')->sendMessage(
    $token,
    $adminChatId,
    "🚨 Serveur à 90% CPU"
);
```

---

## 🎁 Ce que Vous Obtenez

### Code
- ✅ **28 méthodes** API Telegram
- ✅ **5 DTOs** typés (autocomplete IDE)
- ✅ **3 modèles** Eloquent
- ✅ **5 commandes** Artisan
- ✅ **1 canal** de notification
- ✅ **Facade** Laravel

### Outils
- ✅ Long polling (dev local sans webhook)
- ✅ Test rapide (`telebridge:test`)
- ✅ Setup commandes (autocomplete Telegram)
- ✅ Multi-bots (configuration simple)
- ✅ Gestion erreurs (getLastError)
- ✅ Téléchargement fichiers (1 ligne)

### Support
- ✅ **Documentation** : 1500+ lignes
- ✅ **Exemples** : 20+ exemples complets
- ✅ **Guide** : Installation, configuration, déploiement
- ✅ **Community** : GitHub Issues + Discussions

---

## 🚀 Démarrage Rapide

### Étape 1 : Installation

```bash
composer require mbindi/telebridge
php artisan telebridge:install
```

### Étape 2 : Configuration

```env
TELEGRAM_BOT_TOKEN=123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
```

### Étape 3 : Créer Votre Logique

```php
<?php

namespace App\Jobs;

use Mbindi\Telebridge\Services\TelegramClient;

class ProcessTelegramMessage
{
    public function handle(TelegramClient $client)
    {
        // 🔥 VOTRE LOGIQUE ICI
        $response = $this->generateResponse($this->message->content);
        
        // Envoyer
        $client->sendMessage($this->bot->token, $this->chatId, $response);
    }
}
```

### Étape 4 : Tester

```bash
# Dev local
php artisan telebridge:polling

# Ou production
php artisan telebridge:set-webhook
```

**C'est tout ! Votre bot est opérationnel ! 🎉**

---

## 💬 Témoignages

> "Enfin un package Telegram qui me laisse coder comme JE veux !"  
> — **Développeur Laravel**

> "Le long polling m'a fait gagner des heures de config ngrok."  
> — **Startup Tech**

> "Les notifications Laravel intégrées, c'est juste parfait."  
> — **E-commerce Platform**

> "Documentation claire, exemples concrets, ça marche du premier coup."  
> — **Agence Web**

---

## 🎯 Pour Qui ?

### ✅ Parfait Si Vous

- Voulez un bot Telegram dans votre app Laravel
- Utilisez déjà une IA (OpenAI, Claude, DeepSeek, etc.)
- Avez votre propre logique métier
- Voulez garder le contrôle total
- Aimez Laravel standard (Jobs, Notifications, Eloquent)

### ⚠️ Pas Pour Vous Si

- Voulez une solution clé en main avec IA intégrée
- Préférez un framework avec structure imposée
- N'avez pas de logique métier spécifique

---

## 📦 Installation

```bash
composer require mbindi/telebridge
```

---

## 🔗 Liens

- 📖 [Documentation Complète](README.md)
- 🎨 [Présentation Détaillée](PRESENTATION.md)
- 📋 [Liste des Fonctionnalités](FEATURES.md)
- 📝 [Changelog](CHANGELOG.md)
- 🤝 [Guide de Contribution](CONTRIBUTING.md)
- 📦 [Packagist](https://packagist.org/packages/mbindi/telebridge)
- 🐙 [GitHub](https://github.com/mbindi/telebridge)

---

## 📞 Support

- 💬 [GitHub Discussions](https://github.com/mbindi/telebridge/discussions)
- 🐛 [Issues](https://github.com/mbindi/telebridge/issues)
- 📧 Email : contact@mbindi.com

---

## 📜 License

TeleBridge est open-source sous [licence MIT](LICENSE).

---

<div align="center">

### 🌟 Donnez une Étoile !

Si TeleBridge vous aide, **donnez-lui une étoile sur GitHub** ⭐

Ça nous motive à continuer de l'améliorer !

---

**Créé avec ❤️ pour la communauté Laravel**

🚀 **TeleBridge - Le connecteur Telegram qui vous laisse coder** 🚀

[⬆️ Retour en haut](#-telebridge---le-connecteur-telegram-pour-laravel)

</div>

