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
2. Installer le projet (avec composer)
   ```bash
   composer install
   ```
3. Copier les fichiers de config :
   ```bash
   cp .env.dist .env
   cp config/presets.php.dist config/presets.php
   ```

4. Dans `.env`, renseigner les variables :
   ```
   SLACK_CLIENT_ID=...
   SLACK_CLIENT_SECRET=...
   WEB_SECRET_TOKEN=...
   ```

4. Dans ton app Slack (via https://api.slack.com/apps) :
    - Active `OAuth & Permissions`
    - Ajoute `users.profile:write` dans **User Token Scopes**
    - Configure l’URL de redirection dans **Redirect URLs** (ex : http://localhost:8888/callback)

---

## 🔐 Authentification Slack (login)

Lance cette commande pour lier ton compte Slack :

```bash
php bin/slack-status login
```

Tu verras une URL à ouvrir dans ton navigateur.

➡️ Slack te redirigera avec un lien contenant `?code=...`  
➡️ Colle ce code dans le terminal pour enregistrer le token dans `.token`

---

## ✏️ Changer ton statut Slack

```bash
php bin/slack-status change [type]
```

Par exemple :

```bash
php bin/slack-status change sport
php bin/slack-status change reset # Pour réinitialiser son status
```

Le script utilise les presets définis dans `status-presets.php`.

---

## 📋 Lister les statuts disponibles

Pour voir tous les types disponibles depuis les presets :

```bash
php bin/slack-status presets-list
```

---

## ⚙️ Personnaliser les statuts (`config/presets.php`)

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

> 💡 Le fichier `config/presets.php.dist` est versionné, mais pas `config/presets.php` (pense à faire un `cp` au premier lancement).

---

## 🌐 Utilisation web (index.php)

Tu peux aussi déclencher le changement de statut via une URL HTTP, en appelant le script web/index.php.

Exemple d'URL :
```
http://ton-site.local/web/index.php?status=sport&token=webSecretToken
http://ton-site.local/?status=sport&token=webSecretToken
```

Paramètres :
- `status` : le nom du statut à appliquer (doit exister dans les presets)
- `token` : une clé secrète pour authentifier l'appel et éviter les abus (correspondant à la valeur configurée dans WEB_SECRET_TOKEN)

> ⚠️ Le fichier index.php utilise shell_exec() pour appeler bin/slack-status. 
> Assure-toi que les chemins sont bien corrigés selon sa nouvelle position dans /web/.
