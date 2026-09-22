<?php
declare(strict_types=1);
$version = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>AR Game Shooter · Live Tactical View</title>
    <link rel="stylesheet" href="assets/css/game.css?v=<?php echo $version; ?>">

    <!-- Import Maps Polyfill & Libraries -->
    <script async src="https://unpkg.com/es-module-shims@1.6.3/dist/es-module-shims.js"></script>
    <script type="importmap">
      {
        "imports": {
          "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
          "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/",
          "mindar-image-three": "https://cdn.jsdelivr.net/npm/mind-ar@1.2.5/dist/mindar-image-three.prod.js"
        }
      }
    </script>
</head>
<body class="game-body">
    <!-- HUD Overlay -->
    <div id="hud" class="hud-layer">
        <div class="hud-top">
            <div class="hud-stat">SCORE: <span id="scoreVal">0</span></div>
            <div class="hud-stat">WAVE: <span id="waveVal">1</span></div>
            <div class="hud-stat">HP: <span id="hpVal">5</span></div>
            <div class="hud-stat">AMMO: <span id="ammoVal">30/30</span></div>
            <div class="hud-stat" style="display: flex; align-items: center;">
                DIST: 
                <div style="width: 70px; height: 8px; background: #333; margin-left: 6px; border: 1px solid #777; border-radius: 2px; overflow: hidden;">
                    <div id="distanceBar" style="width: 100%; height: 100%; background: #ff3333; transition: width 0.1s;"></div>
                </div>
            </div>
        </div>

        <div id="targetStatus" class="hud-center-status">SCANNING TARGET...</div>

        <div class="crosshair-container">
            <div id="crosshair" class="crosshair"></div>
        </div>

        <div class="hud-bottom">
            <button id="reloadBtn" class="tactical-action-btn btn-reload">RELOAD</button>
            <button id="fireBtn" class="tactical-action-btn btn-fire">FIRE</button>
        </div>

        <!-- Game Over Modal -->
        <div id="gameOverModal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <h2>MISSION FAILED</h2>
                <p>Final Score: <span id="finalScore">0</span></p>
                <p>Wave Reached: <span id="finalWave">1</span></p>
                <button id="restartBtn" class="btn btn-primary btn-block">RESTART MISSION</button>
            </div>
        </div>
    </div>

    <!-- Main Game Script Module -->
    <script type="module" src="assets/js/main.js?v=<?php echo $version; ?>"></script>
</body>
</html>