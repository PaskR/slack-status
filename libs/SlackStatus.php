<?php

namespace App;

use JsonException;

class SlackStatus
{
    private const RESET_PRESET = 'reset';

    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private string $tokenFile;
    private ?string $token;

    private array $presets;

    public function __construct(Config $config)
    {
        $this->clientId = $config->get('SLACK_CLIENT_ID');
        $this->clientSecret = $config->get('SLACK_CLIENT_SECRET');
        $this->redirectUri = $config->get('REDIRECT_URI');
        $this->tokenFile = $config->getTokenPath();
        $this->token = $config->getToken();
        $this->presets = $config->getPresets();
    }

    /**
     * @throws JsonException
     */
    public function authenticate(): void
    {
        writeln("👉 Ouvre cette URL dans ton navigateur :");
        writeln();
        writeln("https://slack.com/oauth/v2/authorize?" . http_build_query(['client_id' => $this->clientId, 'user_scope' => 'users.profile:write', 'redirect_uri' => $this->redirectUri]));
        writeln();
        writeln("✅ Une fois autorisé, colle ici le paramètre code=... de l'URL de redirection :");
        writeln();
        write("> ");
        $code = trim(fgets(STDIN));
        writeln();

        $response = $this->httpPost('https://slack.com/api/oauth.v2.access', http_build_query(['client_id' => $this->clientId, 'client_secret' => $this->clientSecret, 'code' => $code, 'redirect_uri' => $this->redirectUri]));
        if (!empty($response['authed_user']['access_token'])) {
            file_put_contents($this->tokenFile, $response['authed_user']['access_token']);
            writeln("✅ Token Slack enregistré dans .token");
        } else {
            writeln("❌ Erreur lors de l'échange du code :");
            print_r($response);
        }
    }

    /**
     * @throws JsonException
     */
    public function updateStatus(string $type): void
    {
        if (!$this->token) {
            writeln("❌ Aucun token Slack trouvé. Lancez d'abord 'login'.");
            exit(1);
        }

        $reset = false;
        if (!isset($this->presets[$type])) {
            if (self::RESET_PRESET === $type) {
                $status = ['text' => '', 'emoji' => ''];
                $reset = true;
            } else {
                writeln("❌ Type inconnu : $type");
                writeln("Types disponibles : " . implode(', ', array_keys($this->presets)) . " ou " . self::RESET_PRESET . " réinitialiser le status");
                exit(1);
            }
        } else {
            $status = $this->presets[$type];
        }

        $expiration = isset($status['expiration']) ? (time() + ((int)$status['expiration'] * 60)) : 0;
        $response = $this->httpPost(
            'https://slack.com/api/users.profile.set',
            json_encode([
                'profile' => [
                    'status_text' => $status['text'],
                    'status_emoji' => $status['emoji'],
                    'status_expiration' => $expiration,
                ]
            ], JSON_THROW_ON_ERROR),
            [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Bearer ' . $this->token,
            ]
        );

        if ($response['ok'] ?? false) {
            if ($reset) {
                writeln("✅ Statut Slack réinitialisé");
            } else {
                writeln("✅ Statut Slack mis à jour : {$status['emoji']} {$status['text']}" . (0 !== $expiration ? ' (expire le ' . date('d/m/Y à H:i:s', $expiration) . ')' : ''));
            }
        } else {
            writeln("❌ Erreur Slack : " . ($response['error'] ?? 'Réponse inconnue'));
        }
    }

    public function listPresets(): void
    {
        writeln("📋 Statuts disponibles :");
        foreach ($this->presets as $key => $data) {
            writeln("🔸 $key → {$data['emoji']} {$data['text']}");
        }
        writeln("🔸 " . self::RESET_PRESET . " → réinitialisé le status");
    }

    /**
     * @throws JsonException
     */
    private function httpPost(string $url, string $content, array $headers = ['Content-Type: application/x-www-form-urlencoded']): array
    {
        $response = file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $content,
            ]
        ]));

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}