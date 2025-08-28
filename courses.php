<?php include "server.php"; ?>
<?php  
  
if(!$_SESSION['username'])  
{ 
  header("Location: login.php");
}  

// Get the current user's ID
$user_query = "SELECT User_ID FROM user_info WHERE User_Name = ?";
$stmt = $db->prepare($user_query);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$user_result = $stmt->get_result();
$user_row = $user_result->fetch_assoc();
$current_user_id = $user_row['User_ID'];
$stmt->close();

// Get Group courses this user has access to
$selected_group = isset($_GET['group']) ? $_GET['group'] : ''; 

$access_query = "SELECT c.* FROM courses c 
                INNER JOIN course_access ca ON c.id = ca.course_id 
                WHERE ca.user_id = ? AND c.course_group = ?";


$stmt = $db->prepare($access_query);
$stmt->bind_param("is", $current_user_id, $selected_group);
$stmt->execute();
$accessible_courses = $stmt->get_result();

// Store course attributes in session
$_SESSION['course_attributes'] = [];
while ($course = $accessible_courses->fetch_assoc()) {
    // Clean up paths to get attributes
    $pdf_attribute = basename($course['pdf_path']);
    $pdf_attribute = str_replace('.pdf', '', $pdf_attribute); // Remove .pdf extension

    $video_attribute = '';
    if (!empty($course['video_path'])) {
        $video_attribute = basename(rtrim($course['video_path'], '/')); // Remove trailing slash
    }

    $zip_attribute = '';
    if (!empty($course['zip_path'])) {
        $zip_attribute = basename($course['zip_path']);
        $zip_attribute = str_replace('.zip', '', $zip_attribute); // Remove .zip extension
    }

    $_SESSION['course_attributes'][] = [
        'id' => $course['id'],
        'course_name' => $course['course_name'],
        'pdf' => $pdf_attribute,
        'video' => $video_attribute,
        'zip' => $zip_attribute
    ];
}
$stmt->close();

// Store accessible course names in an array (for button display)
$accessible_course_names = array_column($_SESSION['course_attributes'], 'pdf');
?>

<html>
<head>
    <title>Learning Portal</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/courses.css">


</head>
<body>
    <?php include "header.php"; ?>
  <div class="container"> 
  <h1>List Of Courses</h1>
  <div class= "center">
        <table style="text-align:center;"> 
            <tr>
                <?php
                $counter = 0;
                foreach ($_SESSION['course_attributes'] as $course) {
                    if ($counter % 3 == 0 && $counter != 0) {
                        echo '</tr><tr>';
                    }
                    
                    echo '<td style="white-space: nowrap; padding:10px;">';
                    echo '<button class="button" ';
                    echo 'data-pdf="' . htmlspecialchars($course['pdf']) . '.pdf" ';
                    echo 'data-video="' . htmlspecialchars($course['video']) . '" ';
                    echo 'data-zip="' . htmlspecialchars($course['zip']) . '">';
                    echo htmlspecialchars($course['course_name']);
                    echo '</button>';
                    echo '</td>';
                    
                    $counter++;
                }
                ?>
            </tr>
        </table>
    </div>
   </div> 
   

<script src="js/button.js"></script>
</body>
</html>