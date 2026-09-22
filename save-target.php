<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Invalid request method']);
    exit;
}

$data = file_get_contents('php://input');
if (empty($data)) {
    echo json_encode(['ok' => false, 'error' => 'Empty buffer received']);
    exit;
}

$targetDir = __DIR__ . '/assets/targets/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$filePath = $targetDir . 'targets.mind';
if (file_put_contents($filePath, $data) !== false) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Failed to write targets.mind on server']);
}