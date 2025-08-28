<?php
require 'server.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['course_id'])) {
    $course_id = intval($_POST['course_id']);
    
    // First, remove all existing access for this course
    $delete_query = "DELETE FROM course_access WHERE course_id = ?";
    $stmt = $db->prepare($delete_query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    
    // Then add new access permissions
    if (isset($_POST['users']) && is_array($_POST['users'])) {
        $insert_query = "INSERT INTO course_access (course_id, user_id) VALUES (?, ?)";
        $stmt = $db->prepare($insert_query);
        
        foreach ($_POST['users'] as $user_id) {
            $user_id = intval($user_id);
            $stmt->bind_param("ii", $course_id, $user_id);
            $stmt->execute();
        }
    }
    
    $_SESSION['upload_message'] = "Course access updated successfully!";
} else {
    $_SESSION['upload_message'] = "Error updating course access.";
}

header('Location: managecourses.php');
exit();
?> 