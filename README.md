# TeleBridge: Le Framework de Bot Telegram pour Laravel

TeleBridge est un package Laravel conçu pour créer, gérer et étendre des bots Telegram de manière simple et structurée. Il fournit une base solide pour le traitement des messages, la détection d'intentions et l'intégration avec d'autres services.

## ✨ Fonctionnalités

-   **Prêt à l'emploi** : Installez, configurez, et votre bot est prêt à répondre.
-   **Multi-Bot** : Conçu pour gérer plusieurs bots au sein de la même application.
-   **Logique Intelligente** : Système de détection d'intention et de moteur de réponse intégré.
-   **Extensible** : Des services modulaires et un gestionnaire d'intégrations pour connecter des IA, des CRM, etc.
-   **Basé sur Laravel** : S'intègre parfaitement à l'écosystème Laravel (migrations, commandes, configuration).

---

## 🚀 Guide de Démarrage Rapide

Suivez ces étapes pour rendre votre bot opérationnel en quelques minutes.

### Étape 1 : Installation

1.  Ouvrez votre terminal dans un projet Laravel existant et installez le package via Composer :
    ```bash
    composer require mbindi/telebridge
    ```

2.  Exécutez la commande d'installation pour publier le fichier de configuration et lancer les migrations de la base de données :
    ```bash
    php artisan telebridge:install
    ```
    Cela créera les tables `telegram_bots`, `telegram_users`, et `telegram_messages` dans votre base de données.

### Étape 2 : Configuration

1.  **Obtenez un token de bot** : Parlez à [@BotFather](https://t.me/BotFather) sur Telegram pour créer un nouveau bot. Il vous donnera un **token** (jeton d'accès).

2.  **Ajoutez le token à votre environnement** : Ouvrez votre fichier `.env` et ajoutez-y le token :
    ```env
    TELEGRAM_BOT_TOKEN="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11"
    TELEGRAM_WEBHOOK_SECRET="votre_mot_de_passe_secret_pour_securiser_le_webhook"
    ```
    Le `TELEGRAM_WEBHOOK_SECRET` est une chaîne de caractères aléatoire que vous inventez. Elle sera utilisée pour vérifier que les requêtes proviennent bien de Telegram.

### Étape 3 : Exposer votre site et définir le Webhook

Pour que Telegram puisse envoyer des messages à votre application, celle-ci doit être accessible publiquement sur internet.

1.  **Pour le développement local** : Utilisez un outil comme [Ngrok](https://ngrok.com/) pour créer un tunnel sécurisé vers votre machine locale.
    ```bash
    # Expose le port 8000 de votre serveur local
    ngrok http 8000
    ```
    Ngrok vous donnera une URL publique en `https` (ex: `https://abcdef123456.ngrok.io`).

2.  **Définissez le Webhook** : Exécutez la commande Artisan suivante. Elle configurera automatiquement Telegram pour qu'il envoie les mises à jour à votre application.
    ```bash
    php artisan telebridge:set-webhook
    ```
    Le package utilisera l'URL de votre application (définie dans `APP_URL` de votre `.env`) pour construire l'URL du webhook. Assurez-vous que `APP_URL` correspond à votre URL publique (ex: votre URL Ngrok).

### Étape 4 : Testez votre Bot !

Envoyez un message à votre bot sur Telegram. Essayez "hello", "/start", "quel est le prix ?" ou "comment vous contacter ?". Le bot devrait vous répondre !

---

## 🧠 Concepts Clés et Personnalisation

### Le Flux d'un Message

1.  **Webhook** -> `TeleBridgeController` : Le message arrive.
2.  **Controller** -> `MessageRouter` : Le message est transmis au routeur logique.
3.  **Router** -> `IntentDetector` : L'intention est extraite du message.
4.  **Router** -> `ResponseEngine` : Une réponse est générée en fonction de l'intention.
5.  **Router** -> `TelegramClient` : La réponse est envoyée à l'utilisateur.

### Personnaliser les Réponses et les Intentions

Pour débuter, vous pouvez modifier directement les services pour ajouter votre propre logique.

-   **Ajouter une intention** : Ouvrez `src/Services/IntentDetector.php` et ajoutez une nouvelle condition.
    ```php
    // Dans la méthode detect()
    if (str_contains($text, 'aide')) {
        return ['intent' => 'ask_help', 'confidence' => 0.9];
    }
    ```

-   **Ajouter une réponse** : Ouvrez `src/Services/ResponseEngine.php` et ajoutez la réponse correspondante.
    ```php
    // Dans la méthode generate()
    $responses = [
        'ask_price' => 'Nos prix débutent à 100 USD.',
        'ask_contact' => 'Vous pouvez nous contacter à support@example.com.',
        'ask_help' => 'Voici comment je peux vous aider...', // Votre nouvelle réponse
        'unknown' => 'Désolé, je n\'ai pas compris.',
    ];
    ```

### Envoyer un Message Manuellement

Vous pouvez envoyer un message à n'importe quel utilisateur à tout moment en utilisant la façade `TeleBridge`.

```php
use Mbindi\Telebridge\Facades\TeleBridge;

// ... dans un de vos contrôleurs ou services

$botToken = config('telebridge.bot.token');
$chatId = 123456789; // L'ID de l'utilisateur Telegram

TeleBridge::sendMessage($botToken, $chatId, 'Ceci est une notification de votre application !');
```

## 🔧 Commandes Artisan

-   `php artisan telebridge:install` : Installe le package (configuration + migrations).
-   `php artisan telebridge:set-webhook {bot_token?} {--url=}` : Configure le webhook pour un bot. Si aucun argument n'est fourni, il utilise la configuration du fichier `.env`.

## ⚠️ Dépannage

-   **Le webhook ne se configure pas** : Assurez-vous que votre `APP_URL` dans `.env` est correcte et accessible publiquement en `https`.
-   **Les messages ne sont pas reçus** :
    -   Vérifiez les logs de votre application Laravel (`storage/logs/laravel.log`).
    -   Utilisez le tableau de bord de Ngrok (`http://127.0.0.1:4040`) pour inspecter les requêtes entrantes et voir si Telegram envoie bien les données.