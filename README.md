# 📦 slack-status — CLI pour changer ton statut Slack

Un outil en ligne de commande pour :

- 🔐 Authentifier ton compte Slack (OAuth)
- ✏️ Mettre à jour ton statut (`sport`, `pause`, etc.)
- 📋 Lister les statuts disponibles
- ⚙️ Personnaliser facilement les statuts via un fichier dédié

---

## ⚙️ Prérequis

- PHP 7.4+ avec `file_get_contents` activé
- Une application Slack avec le scope `users.profile:write`
- Un fichier `.env` basé sur `.env.dist`

---

## 🚀 Installation rapide

1. **Cloner le projet**
2. Copier les fichiers de config :
   ```bash
   cp .env.dist .env
   cp status-presets.php.dist status-presets.php
   ```

3. Dans `.env`, renseigner les variables :
   ```
   SLACK_CLIENT_ID=...
   SLACK_CLIENT_SECRET=...
   REDIRECT_URI=http://localhost:8888/callback
   ```

4. Dans ton app Slack (via https://api.slack.com/apps) :
    - Active `OAuth & Permissions`
    - Ajoute `users.profile:write` dans **User Token Scopes**
    - Configure l’URL de redirection dans **Redirect URLs** (ex : http://localhost:8888/callback)

---

## 🔐 Authentification Slack (login)

Lance cette commande pour lier ton compte Slack :

```bash
php slack-status login
```

Tu verras une URL à ouvrir dans ton navigateur.

➡️ Slack te redirigera avec un lien contenant `?code=...`  
➡️ Colle ce code dans le terminal pour enregistrer le token dans `.token`

---

## ✏️ Changer ton statut Slack

```bash
php slack-status change [type]
```

Par exemple :

```bash
php slack-status change sport
php slack-status change reset # Pour réinitialiser son status
```

Le script utilise les presets définis dans `status-presets.php`.

---

## 📋 Lister les statuts disponibles

Pour voir tous les types disponibles depuis les presets :

```bash
php slack-status presets-list
```

---

## ⚙️ Personnaliser les statuts (`status-presets.php`)

Tu peux modifier ou ajouter tes propres statuts dans ce fichier :

```php
<?php

return [
    'sport' => [
        'text' => 'En séance de sport',
        'emoji' => ':muscle:',
    ],
    'pause' => [
        'text' => 'Pause café',
        'emoji' => ':coffee:',
    ],
    'focus' => [
        'text' => 'Concentration maximale',
        'emoji' => ':brain:',
    ],
];
```

> 💡 Le fichier `status-presets.php.dist` est versionné, mais pas `status-presets.php` (pense à faire un `cp` au premier lancement).

---

## 🧹 Fichiers sensibles à ne pas versionner (déjà dans `.gitignore`)

```
.env
.token
status-presets.php
```

