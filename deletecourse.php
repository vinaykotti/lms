<?php
require 'server.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $course_id = intval($_GET['id']);
    
    // First, get the PDF file path
    $query = "SELECT pdf_path FROM courses WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $pdf_path = $row['pdf_path'];
        
        // Start transaction
        $db->begin_transaction();
        
        try {
            // Delete from course_access first (due to foreign key constraint)
            $delete_access = "DELETE FROM course_access WHERE course_id = ?";
            $stmt = $db->prepare($delete_access);
            $stmt->bind_param("i", $course_id);
            $stmt->execute();
            
            // Delete from courses table
            $delete_course = "DELETE FROM courses WHERE id = ?";
            $stmt = $db->prepare($delete_course);
            $stmt->bind_param("i", $course_id);
            $stmt->execute();
            
            // If database operations successful, delete the PDF file
            if (file_exists($pdf_path)) {
                unlink($pdf_path);
            }
            
            // Commit transaction
            $db->commit();
            
            $_SESSION['upload_message'] = "Course deleted successfully!";
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $db->rollback();
            $_SESSION['upload_message'] = "Error deleting course: " . $e->getMessage();
        }
    } else {
        $_SESSION['upload_message'] = "Course not found!";
    }
    
    $stmt->close();
} else {
    $_SESSION['upload_message'] = "Invalid request!";
}

// Redirect back to manage courses page
header('Location: managecourses.php');
exit();
?> 