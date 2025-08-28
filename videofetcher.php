<?php

// serve-video.php
$baseDirectory = "videos"; // Base directory containing subfolders

$folder = isset($_GET['video']) ? basename($_GET['video']) : '';
$file = isset($_GET['file']) ? basename($_GET['file']) : '';

$path = $baseDirectory . '/' . $folder . '/' . $file;

if (!empty($folder) && file_exists($path) && preg_match('/\.(mp4|avi|mov|mkv|webm)$/i', $file)) {
    // Limit file size (e.g., 500MB)
    if (filesize($path) > 500 * 1024 * 1024) {
        http_response_code(413);
        echo "File too large.";
        exit;
    }

    // Dynamically determine the correct MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $path);
    finfo_close($finfo);
    
    header("Content-Type: $mimeType");
    header('Accept-Ranges: bytes'); // Enables seeking
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');

    $filesize = filesize($path);

    // Handle range requests for efficient streaming
    if (isset($_SERVER['HTTP_RANGE'])) {
        list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
        list($start, $end) = explode('-', $range);

        $start = intval($start);
        $end = ($end === '') ? $filesize - 1 : intval($end);
        $length = $end - $start + 1;

        header('HTTP/1.1 206 Partial Content');
        header("Content-Range: bytes $start-$end/$filesize");
        header('Content-Length: ' . $length);

        $fp = fopen($path, 'rb');
        fseek($fp, $start);
        echo fread($fp, $length);
        fclose($fp);
        exit;
    }

    // Serve the full file if no range request
    header("Content-Length: $filesize");
    readfile($path);
    exit;
} else {
    // Log failed access attempts
    error_log("File access attempt failed: $path");

    http_response_code(404);
    echo 'File not found.';
}

?>
