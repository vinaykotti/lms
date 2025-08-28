<?php
require 'server.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['course_id']) && isset($_FILES['new_pdf'])) {
    $course_id = intval($_POST['course_id']);
    
    // First, get the current course information
    $query = "SELECT course_name, pdf_path FROM courses WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    
    if ($course) {
        $file = $_FILES['new_pdf'];
        
        // Use the existing pdf_path to maintain the same filename
        $target_path = $course['pdf_path'];
        
        try {
            // Start transaction
            $db->begin_transaction();
            
            // Create a backup of the old file with temporary name
            if (file_exists($target_path)) {
                $backup_path = $target_path . '.bak';
                rename($target_path, $backup_path);
            }
            
            // Move the new file
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                // Successfully replaced the file, delete the backup
                if (isset($backup_path) && file_exists($backup_path)) {
                    unlink($backup_path);
                }
                
                $db->commit();
                $_SESSION['upload_message'] = "Course PDF updated successfully!";
            } else {
                // If upload failed, restore the backup
                if (isset($backup_path) && file_exists($backup_path)) {
                    rename($backup_path, $target_path);
                }
                throw new Exception("Error uploading file.");
            }
            
        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['upload_message'] = "Error updating course PDF: " . $e->getMessage();
            
            // Restore backup if it exists
            if (isset($backup_path) && file_exists($backup_path)) {
                rename($backup_path, $target_path);
            }
        }
    } else {
        $_SESSION['upload_message'] = "Course not found!";
    }
    
    $stmt->close();
} else {
    $_SESSION['upload_message'] = "Invalid request!";
}

header('Location: managecourses.php');
exit();
?> 