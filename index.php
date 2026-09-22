<?php
declare(strict_types=1);
require_once __DIR__ . '/api/site_lib.php';
$gameUrl = get_game_url();
$targetMindPath = __DIR__ . '/assets/targets/targets.mind';
$targetExists = file_exists($targetMindPath);
$mindVersion = $targetExists ? filemtime($targetMindPath) : time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AR Game Shooter · Home</title>
    <link rel="stylesheet" href="assets/css/game.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body class="tactical-body">
    <header class="tactical-header">
        <div class="logo">ARGAME <span>TACTICAL</span></div>
        <nav>
            <a href="index.php" class="active">Home</a>
            <a href="game.php" target="_blank">Play</a>
            <a href="models.php">Models</a>
            <a href="compile-target.php">Compile</a>
        </nav>
    </header>

    <main class="tactical-container">
        <section class="hero-section">
            <h1>AR GAME SHOOTER</h1>
            <p class="subtitle">Web AR · Image Target First-Person Tactical Shooter</p>
            <p class="desc">Scan the QR code with your smartphone, point your rear camera at the image target, and engage 3D enemy waves in immersive augmented reality.</p>
            
            <div class="hero-actions">
                <a href="game.php" class="btn btn-primary" target="_blank">Start Game</a>
                <a href="models.php" class="btn btn-secondary">Change Models</a>
                <a href="compile-target.php" class="btn btn-accent">Compile Target</a>
            </div>
        </section>

        <div class="grid-dashboard">
            <div class="panel">
                <h2>Mobile Access QR Code</h2>
                <p>Scan this code with your mobile device camera to launch the AR game instance:</p>
                <div id="qrcode" class="qr-box"></div>
                <div class="url-display">
                    <input type="text" id="gameUrlInput" value="<?php echo htmlspecialchars($gameUrl, ENT_QUOTES); ?>" readonly>
                    <button class="btn btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('gameUrlInput').value); alert('URL Copied!');">Copy Link</button>
                </div>
            </div>

            <div class="panel">
                <h2>AR Target Preview</h2>
                <p>Status: <span class="badge <?php echo $targetExists ? 'badge-success' : 'badge-danger'; ?>"><?php echo $targetExists ? 'Compiled · v' . $mindVersion : 'Not compiled · Upload & Compile'; ?></span></p>
                <div class="target-preview-box">
                    <a href="assets/targets/picture.jpg?v=<?php echo $mindVersion; ?>" target="_blank">
                        <img src="assets/targets/picture.jpg?v=<?php echo $mindVersion; ?>" alt="AR Target Image" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'300\' height=\'200\'><rect width=\'100%\' height=\'100%\' fill=\'%23222\'/><text x=\'50%\' y=\'50%\' fill=\'%23aaa\' dominant-baseline=\'middle\' text-anchor=\'middle\'>No Target Image Found</text></svg>';">
                    </a>
                </div>
                <form action="upload-target.php" method="POST" enctype="multipart/form-data" class="upload-form">
                    <label for="targetFile">Upload New Target (JPG/PNG/WEBP, Max 12MB):</label>
                    <input type="file" name="targetFile" id="targetFile" accept=".jpg, .jpeg, .png, .webp" required>
                    <div class="checkbox-row">
                        <label><input type="checkbox" name="autoCompile" value="1" checked> Auto-compile after upload</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Upload Target</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        const gameUrl = document.getElementById('gameUrlInput').value;
        new QRCode(document.getElementById("qrcode"), {
            text: gameUrl,
            width: 180,
            height: 180,
            colorDark: "#00ffcc",
            colorLight: "#0b0f19",
            correctLevel: QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>