<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

require_once __DIR__ . '/models_lib.php';

$config = get_models_config();
$scanned = get_scanned_models();

echo json_encode([
    'ok' => true,
    'config' => $config,
    'enemyRandom' => ($config['enemy'] === 'random'),
    'weaponRandom' => ($config['weapon'] === 'random'),
    'enemyCandidates' => $scanned['enemies'],
    'weaponCandidates' => $scanned['weapons'],
    'enemyUrl' => 'assets/models/' . ($config['enemy'] === 'random' ? $scanned['enemies'][array_rand($scanned['enemies'])] : $config['enemy']),
    'weaponUrl' => 'assets/models/' . ($config['weapon'] === 'random' ? $scanned['weapons'][array_rand($scanned['weapons'])] : $config['weapon'])
]);