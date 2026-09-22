<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['targetFile'])) {
    $file = $_FILES['targetFile'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die('Upload error code: ' . $file['error']);
    }
    
    if ($file['size'] > 12 * 1024 * 1024) {
        die('File exceeds 12MB limit.');
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        die('Invalid image format. Allowed: JPG, PNG, WEBP.');
    }
    
    $targetDir = __DIR__ . '/assets/targets/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $destination = $targetDir . 'picture.jpg';
    
    // Normalize image to true JPG using GD if available, or direct move
    $img = null;
    if ($mime === 'image/jpeg') $img = imagecreatefromjpeg($file['tmp_name']);
    elseif ($mime === 'image/png') $img = imagecreatefrompng($file['tmp_name']);
    elseif ($mime === 'image/webp') $img = imagecreatefromwebp($file['tmp_name']);
    
    if ($img) {
        imagejpeg($img, $destination, 90);
        imagedestroy($img);
    } else {
        move_uploaded_file($file['tmp_name'], $destination);
    }
    
    $autoCompile = isset($_POST['autoCompile']) && $_POST['autoCompile'] === '1';
    if ($autoCompile) {
        header('Location: compile-target.php');
        exit;
    }
    
    header('Location: index.php');
    exit;
}
header('Location: index.php');