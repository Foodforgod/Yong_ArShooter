<?php
declare(strict_types=1);

function get_models_config(): array {
    $file = __DIR__ . '/../assets/config/models.json';
    if (!file_exists($file)) {
        return ['enemy' => 'random', 'weapon' => 'fps-akm.glb'];
    }
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : ['enemy' => 'random', 'weapon' => 'fps-akm.glb'];
}

function save_models_config(array $config): bool {
    $file = __DIR__ . '/../assets/config/models.json';
    $config['updatedAt'] = date('c');
    return file_put_contents($file, json_encode($config, JSON_PRETTY_PRINT)) !== false;
}

function get_scanned_models(): array {
    $baseDir = __DIR__ . '/../assets/models/';
    $enemies = [];
    $weapons = [];
    
    // 1. Scan weapons subfolder
    $weaponsDir = $baseDir . 'weapons/';
    if (is_dir($weaponsDir)) {
        foreach (scandir($weaponsDir) as $f) {
            if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'glb') {
                $weapons[] = $f;
            }
        }
    }
    
    // 2. Scan enemies subfolder
    $enemiesDir = $baseDir . 'enemies/';
    if (is_dir($enemiesDir)) {
        foreach (scandir($enemiesDir) as $f) {
            if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'glb') {
                $enemies[] = $f;
            }
        }
    }
    
    // 3. Fallback: Scan root models directory (for any legacy files)
    if (is_dir($baseDir)) {
        foreach (scandir($baseDir) as $f) {
            if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'glb') {
                if (stripos($f, 'fps') !== false || stripos($f, 'weapon') !== false || stripos($f, 'rig') !== false) {
                    if (!in_array($f, $weapons)) $weapons[] = $f;
                } else {
                    if (!in_array($f, $enemies)) $enemies[] = $f;
                }
            }
        }
    }
    
    if (empty($enemies)) $enemies[] = 'orc-enemy.glb';
    if (empty($weapons)) $weapons[] = 'fps-akm.glb';
    
    // Sort alphabetically for clean dropdowns
    sort($enemies);
    sort($weapons);
    
    return ['enemies' => $enemies, 'weapons' => $weapons];
}