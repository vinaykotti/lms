<?php

$baseDirectory = "videos"; // Base directory

$folder = isset($_GET['video']) ? basename($_GET['video']) : '';

$path = $baseDirectory . '/' . $folder;

if (!empty($folder) && is_dir($path)) {
    $files = array_values(array_filter(scandir($path), function ($file) use ($path) {
        return is_file($path . '/' . $file) && preg_match('/\.(mp4|avi|mov|mkv|webm)$/i', $file);
    }));
    echo json_encode($files);
} else {
    echo json_encode([]);
}

?>
