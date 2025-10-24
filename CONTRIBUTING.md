# Contribution à TeleBridge

Merci de votre intérêt pour contribuer à TeleBridge ! 🎉

## 🎯 Philosophie du Package

**TeleBridge est un connecteur pur pour Telegram.**

### ✅ Contributions Acceptées

- Nouvelles méthodes de l'API Telegram Bot
- Amélioration de la gestion des webhooks
- Support de nouveaux types de messages Telegram
- Optimisations de performance
- Corrections de bugs
- Amélioration de la documentation
- Exemples d'utilisation
- Tests unitaires

### ❌ Contributions Refusées

- Logique métier (IA, génération de réponses, etc.)
- Systèmes de licence/quota (à implémenter dans l'application)
- Bases de connaissances
- Systèmes d'analytics
- Toute fonctionnalité qui n'est pas directement liée à la communication avec l'API Telegram

> **Principe** : Si ça n'est pas directement lié à la communication avec Telegram, ça ne doit pas être dans TeleBridge.

---

## 🚀 Comment Contribuer

### 1. Fork le Repository

Forkez le repository sur votre compte GitHub.

### 2. Clonez Votre Fork

```bash
git clone https://github.com/votre-username/telebridge.git
cd telebridge
```

### 3. Installez les Dépendances

```bash
composer install
```

### 4. Créez une Branche

```bash
git checkout -b feature/ma-nouvelle-fonctionnalite
```

### 5. Faites Vos Modifications

Assurez-vous de :
- Suivre le style de code PSR-12
- Ajouter des commentaires clairs
- Tester votre code
- Mettre à jour la documentation si nécessaire

### 6. Committez Vos Changements

```bash
git add .
git commit -m "feat: Description de la fonctionnalité"
```

Utilisez les préfixes de commit conventionnels :
- `feat:` - Nouvelle fonctionnalité
- `fix:` - Correction de bug
- `docs:` - Documentation
- `refactor:` - Refactoring
- `test:` - Tests
- `chore:` - Maintenance

### 7. Pushez Votre Branche

```bash
git push origin feature/ma-nouvelle-fonctionnalite
```

### 8. Créez une Pull Request

Créez une Pull Request depuis votre fork vers le repository principal.

---

## 📝 Standards de Code

### PSR-12

TeleBridge suit les standards PSR-12 pour le style de code PHP.

### Nommage

- **Classes** : PascalCase (`TelegramClient`)
- **Méthodes** : camelCase (`sendMessage`)
- **Variables** : camelCase (`$chatId`)
- **Constantes** : UPPER_SNAKE_CASE (`API_VERSION`)

### Documentation

Documentez vos méthodes publiques avec PHPDoc :

```php
/**
 * Envoie un message texte
 *
 * @param string $token Token du bot
 * @param int $chatId ID du chat
 * @param string $text Texte du message
 * @param array $options Options additionnelles
 * @return array|null Réponse de l'API ou null en cas d'erreur
 */
public function sendMessage(string $token, int $chatId, string $text, array $options = []): ?array
{
    // ...
}
```

---

## 🧪 Tests

### Exécuter les Tests

```bash
composer test
```

### Ajouter des Tests

Si vous ajoutez une nouvelle fonctionnalité, ajoutez des tests dans le dossier `tests/`.

---

## 📋 Checklist de Pull Request

Avant de soumettre votre PR, assurez-vous que :

- [ ] Le code suit les standards PSR-12
- [ ] Les méthodes publiques sont documentées
- [ ] Les tests passent
- [ ] La documentation est à jour (README, CHANGELOG)
- [ ] Pas de logique métier ajoutée (seulement communication Telegram)
- [ ] Le commit suit les conventions
- [ ] La description de la PR est claire

---

## 🐛 Signaler un Bug

Utilisez les [GitHub Issues](https://github.com/mbindi/telebridge/issues) avec :
- Description claire du bug
- Étapes pour reproduire
- Version de PHP et Laravel
- Version de TeleBridge
- Message d'erreur complet

---

## 💡 Proposer une Fonctionnalité

Avant de coder une nouvelle fonctionnalité :
1. Ouvrez une issue pour discuter de la fonctionnalité
2. Assurez-vous qu'elle respecte la philosophie du package
3. Attendez l'approbation avant de commencer le développement

---

## 📞 Questions ?

- **Issues** : [GitHub Issues](https://github.com/mbindi/telebridge/issues)
- **Discussions** : [GitHub Discussions](https://github.com/mbindi/telebridge/discussions)

---

Merci de contribuer à TeleBridge ! 🙏

