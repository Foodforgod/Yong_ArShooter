<?php
declare(strict_types=1);

function get_site_config(): array {
    $file = __DIR__ . '/../assets/config/site.json';
    if (!file_exists($file)) {
        return ['publicBaseUrl' => ''];
    }
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : ['publicBaseUrl' => ''];
}

function get_game_url(): string {
    $config = get_site_config();
    if (!empty($config['publicBaseUrl'])) {
        return rtrim($config['publicBaseUrl'], '/') . '/game.php';
    }
    
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = dirname($_SERVER['PHP_SELF'] ?? '/');
    $path = rtrim(str_replace('\\', '/', $uri), '/');
    
    return $protocol . $host . $path . '/game.php';
}