<?php
require 'server.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['course_id']) && isset($_POST['course_name'])) {
    $course_id = intval($_POST['course_id']);
    $new_course_name = trim($_POST['course_name']);
    
    // Validate new course name
    if (empty($new_course_name)) {
        $_SESSION['upload_message'] = "Course name cannot be empty!";
        header('Location: managecourses.php');
        exit();
    }
    
    try {
        // Start transaction
        $db->begin_transaction();
        
        // Get current course info
        $query = "SELECT course_name FROM courses WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $course = $result->fetch_assoc();
        
        if ($course) {
            // Update course name
            $update_query = "UPDATE courses SET course_name = ? WHERE id = ?";
            $stmt = $db->prepare($update_query);
            $stmt->bind_param("si", $new_course_name, $course_id);
            
            if ($stmt->execute()) {
                $db->commit();
                $_SESSION['upload_message'] = "Course name updated successfully!";
            } else {
                throw new Exception("Error updating course name.");
            }
        } else {
            throw new Exception("Course not found!");
        }
        
    } catch (Exception $e) {
        $db->rollback();
        $_SESSION['upload_message'] = "Error updating course name: " . $e->getMessage();
    }
    
    $stmt->close();
} else {
    $_SESSION['upload_message'] = "Invalid request!";
}

header('Location: managecourses.php');
exit();
?> 