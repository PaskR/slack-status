<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config;
use App\Output;

if (empty($status = $_GET['status'] ?? null)) {
    http_response_code(400);
    Output::writeln("❌ Paramètre ?status= manquant");
    exit;
}

if ((new Config())->getEnv('WEB_SECRET_TOKEN') !== ($_GET['token'] ?? null)) {
    http_response_code(403);
    Output::writeln("⛔️ Accès refusé");
    exit;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['output' => shell_exec(sprintf('%s change %s', __DIR__ . '/bin/slack-status', escapeshellarg($status)))], JSON_THROW_ON_ERROR);
