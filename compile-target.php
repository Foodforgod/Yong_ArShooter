<?php
declare(strict_types=1);
$targetExists = file_exists(__DIR__ . '/assets/targets/picture.jpg');
$mindExists = file_exists(__DIR__ . '/assets/targets/targets.mind');$version = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compile AR Target</title>
    <link rel="stylesheet" href="assets/css/game.css">
</head>
<body class="tactical-body">
    <header class="tactical-header">
        <div class="logo">ARGAME <span>COMPILER</span></div>
        <nav>
            <a href="index.php">Home</a>
            <a href="game.php" target="_blank">Play</a>
            <a href="models.php">Models</a>
            <a href="compile-target.php" class="active">Compile</a>
        </nav>
    </header>

    <main class="tactical-container">
        <div class="panel compiler-panel">
            <h2>MindAR Target Compiler</h2>
            <p>Compile <code>assets/targets/picture.jpg</code> into <code>targets.mind</code> for browser-based AR tracking.</p>
            
            <?php if (!$targetExists): ?>
                <p class="error-text">Target image <code>picture.jpg</code> not found! Please upload one on the home page first.</p>
                <a href="index.php" class="btn btn-primary">Go to Home</a>
            <?php else: ?>
                <div class="target-preview-box">
                    <img id="sourceImage" src="assets/targets/picture.jpg?v=<?php echo $version; ?>" alt="Target">
                </div>
                <div id="compilerStatus" class="status-box">
                    <?php echo $mindExists ? "Ready to compile or use existing target." : "Ready to compile target image..."; ?>
                </div>
                <div class="progress-bar-container">
                    <div id="progressBar" class="progress-bar" style="width: 0%;"></div>
                </div>
                <button id="compileBtn" class="btn btn-primary btn-block">Start Compilation</button>
                
                <?php if ($mindExists): ?>
                    <div style="margin-top: 15px; text-align: center;">
                        <a href="game.php" class="btn btn-success btn-block" style="background: #28a745; color: white; padding: 10px; display: block; text-decoration: none; border-radius: 4px;">Launch Game (targets.mind already active)</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Fallback CDN Script -->
    <script src="https://cdn.jsdelivr.net/npm/mind-ar@1.1.5/dist/mindar-image-compiler.prod.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const compileBtn = document.getElementById('compileBtn');
            if (compileBtn) {
                compileBtn.addEventListener('click', startCompilation);
            }
        });

        async function startCompilation() {
            const btn = document.getElementById('compileBtn');
            const status = document.getElementById('compilerStatus');
            const bar = document.getElementById('progressBar');
            const imgEl = document.getElementById('sourceImage');
            
            btn.disabled = true;
            status.innerText = "Loading target image into compiler...";
            bar.style.width = "105%";

            try {
                if (typeof MINDAR === 'undefined' || !MINDAR.IMAGE || !MINDAR.IMAGE.Compiler) {
                    throw new Error("MINDAR compiler script blocked by network/CDN. Since targets.mind is already in your folder, you can click 'Launch Game' below!");
                }

                const img = new Image();
                img.crossOrigin = "anonymous";
                img.src = imgEl.src;
                await img.decode();

                status.innerText = "Initializing MindAR Compiler...";
                bar.style.width = "25%";

                const compiler = new MINDAR.IMAGE.Compiler();
                
                await compiler.compileImageTargets([img], (progress) => {
                    const pct = Math.round(25 + progress * 0.6);
                    bar.style.width = pct + "%";
                    status.innerText = `Compiling features... ${pct}%`;
                });

                status.innerText = "Exporting compiled buffer data...";
                bar.style.width = "90%";
                const buffer = await compiler.exportData();

                status.innerText = "Saving targets.mind to server...";
                const response = await fetch('save-target.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/octet-stream' },
                    body: buffer
                });

                const result = await response.json();
                if (result.ok) {
                    bar.style.width = "100%";
                    status.innerText = "Compilation successful! targets.mind generated.";
                    btn.innerText = "Compilation Complete";
                    btn.classList.add('btn-success');
                } else {
                    throw new Error(result.error || 'Server save failed');
                }
            } catch (err) {
                console.error(err);
                status.innerText = err.message;
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>