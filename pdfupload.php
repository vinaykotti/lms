<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'server.php'; // This now gives us access to $db connection

    $course_name = mysqli_real_escape_string($db, $_POST['course_name']); // Added SQL injection protection
	$course_ID = mysqli_real_escape_string($db, $_POST['course_ID']); // Added SQL injection protection
    $course_group = mysqli_real_escape_string($db, $_POST['course_group']); // Get the selected group
    $sanitized_name = preg_replace('/[^A-Za-z0-9]/', '', $course_name); // Remove spaces & special characters
    $upload_dir = "pdfs/"; // All PDFs go into this folder

    $file = $_FILES['pdf_file'];
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION); // Get file extension
    $new_file_name = $sanitized_name . "." . $file_extension; // Rename PDF to course name
    $target_path = $upload_dir . $new_file_name;

    // Ensure unique filename if the same course name is uploaded again
    $counter = 1;
    while (file_exists($target_path)) {
        $new_file_name = $sanitized_name . $counter . "." . $file_extension;
        $target_path = $upload_dir . $new_file_name;
        $counter++;
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // Updated to use $db instead of $conn
        $query = "INSERT INTO courses (course_name, pdf_path, course_group, course_ID) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssss", $course_name, $target_path, $course_group, $course_ID); // Bind course group
        $stmt->execute();
        $stmt->close();
        
        // Set a success message in session and redirect
        $_SESSION['upload_message'] = "File uploaded successfully!";
        header('Location: managecourses.php');
        exit();
    } else {
        $_SESSION['upload_message'] = "Error uploading file.";
        header('Location: managecourses.php');
        exit();
    }

    // No need to close $db as it's handled in server.php
}
?>
