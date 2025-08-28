<?php
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
    
    if ($course && isset($_FILES['zip_file']) && $_FILES['zip_file']['error'] == 0) {
        try {
            $sanitized_name = preg_replace('/[^A-Za-z0-9]/', '', $course['course_name']);
            $zip_path = "workingfiles/" . $sanitized_name . ".zip";
            
            // Create workingfiles directory if it doesn't exist
            if (!file_exists("workingfiles/")) {
                mkdir("workingfiles/", 0777, true);
            }
            
            // Remove existing ZIP if it exists
            if (file_exists($zip_path)) {
                unlink($zip_path);
            }
            
            if (move_uploaded_file($_FILES['zip_file']['tmp_name'], $zip_path)) {
                // Update database with ZIP path
                $update_query = "UPDATE courses SET zip_path = ? WHERE id = ?";
                $stmt = $db->prepare($update_query);
                $stmt->bind_param("si", $zip_path, $course_id);
                $stmt->execute();
                
                $_SESSION['upload_message'] = "ZIP file uploaded successfully!";
            } else {
                throw new Exception("Error moving ZIP file.");
            }
            
        } catch (Exception $e) {
            $_SESSION['upload_message'] = "Error uploading ZIP: " . $e->getMessage();
        }
    } else {
        $_SESSION['upload_message'] = "Course not found or no ZIP file selected!";
    }
    
    header('Location: managecourses.php');
    exit();
}
?> 