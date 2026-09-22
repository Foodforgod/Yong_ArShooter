<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Invalid request method']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!is_array($data) || !isset($data['publicBaseUrl'])) {
    echo json_encode(['ok' => false, 'error' => 'Invalid configuration payload']);
    exit;
}

$configDir = __DIR__ . '/assets/config/';
if (!is_dir($configDir)) {
    mkdir($configDir, 0755, true);
}

$configFile = $configDir . 'site.json';
$siteConfig = [
    'publicBaseUrl' => trim($data['publicBaseUrl']),
    'updatedAt' => date('c')
];

if (file_put_contents($configFile, json_encode($siteConfig, JSON_PRETTY_PRINT)) !== false) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Failed to write site.json configuration']);
}