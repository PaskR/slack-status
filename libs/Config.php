<?php

namespace App;

class Config
{
    private array $env;
    private string $tokenPath;
    private array $presets;

    public function __construct(string $envPath = __DIR__ . '/../.env', string $tokenPath = __DIR__ . '/../.token', string $presetsPath = __DIR__ . '/../presets.php')
    {
        $this->env = $this->loadEnv($envPath);
        $this->tokenPath = $tokenPath;
        $this->presets = file_exists($presetsPath)
            ? require $presetsPath
            : require $presetsPath . '.dist';
    }

    private function loadEnv(string $path): array
    {
        if (!file_exists($path)) {
            writeln("Fichier .env introuvable : $path");
            exit(1);
        }

        $vars = [];
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (0 === strpos(trim($line), '#')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $vars[trim($key)] = trim($value);
        }
        return $vars;
    }

    public function get(string $key): ?string
    {
        return $this->env[$key] ?? null;
    }

    public function getToken(): ?string
    {
        return file_exists($this->tokenPath) ? trim(file_get_contents($this->tokenPath)) : null;
    }

    public function getTokenPath(): string
    {
        return $this->tokenPath;
    }

    public function getPresets(): array
    {
        return $this->presets;
    }
}
