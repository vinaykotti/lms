<?php
// Place these lines at the very top of the file
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Increase PHP limits for this script
ini_set('upload_max_filesize', '2048M');
ini_set('post_max_size', '2048M');
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', '3600');
ini_set('max_input_time', '3600');
set_time_limit(3600);

// Increase nginx/apache timeout
if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', 1);
    apache_setenv('dont-vary', 1);
}

// Check if the upload size exceeds the limit
if (empty($_POST) && empty($_FILES) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $_SESSION['upload_message'] = 'Error: Upload size exceeds limit. Please check your server configuration.';
    header('Location: managecourses.php');
    exit();
}

require 'server.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['course_id'])) {
    $course_id = intval($_POST['course_id']);
    
    // Get course information
    $query = "SELECT course_name FROM courses WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    
    if ($course && isset($_FILES['video_files'])) {
        try {
            $sanitized_name = preg_replace('/[^A-Za-z0-9]/', '', $course['course_name']);
            $video_dir = "videos/" . $sanitized_name . "/";
            
            // Create directory if it doesn't exist
            if (!file_exists($video_dir)) {
                mkdir($video_dir, 0777, true);
            }
            
            $uploaded_files = 0;
            $total_size = 0;
            $errors = [];
            
            foreach ($_FILES['video_files']['tmp_name'] as $key => $tmp_name) {
                $file_size = $_FILES['video_files']['size'][$key];
                $file_error = $_FILES['video_files']['error'][$key];
                $video_name = $_FILES['video_files']['name'][$key];
                $video_path = $video_dir . basename($video_name);
                
                // Check for upload errors
                if ($file_error === UPLOAD_ERR_INI_SIZE) {
                    $errors[] = "$video_name: File exceeds PHP.ini upload_max_filesize";
                    continue;
                } elseif ($file_error === UPLOAD_ERR_FORM_SIZE) {
                    $errors[] = "$video_name: File exceeds form MAX_FILE_SIZE";
                    continue;
                } elseif ($file_error === UPLOAD_ERR_PARTIAL) {
                    $errors[] = "$video_name: File was only partially uploaded";
                    continue;
                } elseif ($file_error === UPLOAD_ERR_NO_FILE) {
                    $errors[] = "$video_name: No file was uploaded";
                    continue;
                }
                
                // Attempt to move the file
                if (move_uploaded_file($tmp_name, $video_path)) {
                    $uploaded_files++;
                    $total_size += $file_size;
                } else {
                    $errors[] = "$video_name: Failed to move uploaded file";
                }
            }
            
            // Update database with video directory path
            if ($uploaded_files > 0) {
                $update_query = "UPDATE courses SET video_path = ? WHERE id = ?";
                $stmt = $db->prepare($update_query);
                $stmt->bind_param("si", $video_dir, $course_id);
                $stmt->execute();
                
                $success_message = "Successfully uploaded " . $uploaded_files . " video(s)! ";
                $success_message .= "Total size: " . round($total_size / (1024 * 1024 * 1024), 2) . " GB";
                
                if (!empty($errors)) {
                    $success_message .= "\nWarnings:\n" . implode("\n", $errors);
                }
                
                $_SESSION['upload_message'] = $success_message;
            } else {
                throw new Exception("No videos were uploaded successfully. Errors:\n" . implode("\n", $errors));
            }
            
        } catch (Exception $e) {
            $_SESSION['upload_message'] = "Error uploading videos: " . $e->getMessage();
        }
    } else {
        $_SESSION['upload_message'] = "Course not found or no videos selected!";
    }
    
    header('Location: managecourses.php');
    exit();
}

if ($upload_success) {
    $_SESSION['success'] = "Video uploaded successfully!";
} else {
    $_SESSION['error'] = "Error uploading video!";
}

// Redirect back to the manage courses page or wherever appropriate
header("Location: managecourses.php");
exit();
?> 