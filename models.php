<?php
declare(strict_types=1);
require_once __DIR__ . '/api/models_lib.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_config'])) {
        $config = [
            'enemy' => $_POST['enemy'] ?? 'random',
            'weapon' => $_POST['weapon'] ?? 'fps-akm.glb'
        ];
        save_models_config($config);
        $message = 'Configuration updated successfully!';
    } elseif (isset($_FILES['modelFile'])) {
        $file = $_FILES['modelFile'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'glb') {
                $message = 'Error: Only .glb files are allowed.';
            } else {
                // Verify GLB header magic 'glTF'
                $handle = fopen($file['tmp_name'], 'rb');
                $magic = fread($handle, 4);
                fclose($handle);
                
                if ($magic !== 'glTF') {
                    $message = 'Error: Invalid GLB file magic signature.';
                } else {
                    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', basename($file['name']));
                    $dest = __DIR__ . '/assets/models/' . $filename;
                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        $message = 'Model uploaded successfully: ' . htmlspecialchars($filename);
                    } else {
                        $message = 'Error moving uploaded file.';
                    }
                }
            }
        }
    }
}

$currentConfig = get_models_config();
$scanned = get_scanned_models();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Model Manager</title>
    <link rel="stylesheet" href="assets/css/game.css">
</head>
<body class="tactical-body">
    <header class="tactical-header">
        <div class="logo">ARGAME <span>MODELS</span></div>
        <nav>
            <a href="index.php">Home</a>
            <a href="game.php" target="_blank">Play</a>
            <a href="models.php" class="active">Models</a>
            <a href="compile-target.php">Compile</a>
        </nav>
    </header>

    <main class="tactical-container">
        <div class="panel">
            <h2>3D Model Manager</h2>
            <?php if (!empty($message)): ?>
                <div class="status-box"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" class="tactical-form">
                <div class="form-group">
                    <label for="enemy">Enemy Selection Model:</label>
                    <select name="enemy" id="enemy" class="form-control">
                        <option value="random" <?php echo $currentConfig['enemy'] === 'random' ? 'selected' : ''; ?>>⭐ Random (Rotates through models per wave)</option>
                        <?php foreach ($scanned['enemies'] as $e): ?>
                            <option value="<?php echo htmlspecialchars($e); ?>" <?php echo $currentConfig['enemy'] === $e ? 'selected' : ''; ?>><?php echo htmlspecialchars($e); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="weapon">FPS Weapon Model:</label>
                    <select name="weapon" id="weapon" class="form-control">
                        <option value="random" <?php echo $currentConfig['weapon'] === 'random' ? 'selected' : ''; ?>>⭐ Random</option>
                        <?php foreach ($scanned['weapons'] as $w): ?>
                            <option value="<?php echo htmlspecialchars($w); ?>" <?php echo $currentConfig['weapon'] === $w ? 'selected' : ''; ?>><?php echo htmlspecialchars($w); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" name="save_config" class="btn btn-primary">Save Configuration</button>
            </form>

            <hr class="tactical-divider">

            <h3>Upload New GLB Model</h3>
            <form method="POST" enctype="multipart/form-data" class="tactical-form">
                <div class="form-group">
                    <label for="modelFile">Select .glb 3D Model File (Max 40MB):</label>
                    <input type="file" name="modelFile" id="modelFile" accept=".glb" required class="form-control-file">
                </div>
                <button type="submit" class="btn btn-secondary">Upload GLB</button>
            </form>
        </div>
    </main>
</body>
</html>