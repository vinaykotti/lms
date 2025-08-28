
<!DOCTYPE html>
<?php include "server.php"; ?> 
<?php
  // Fetch the current user's role
  $current_user_query = "SELECT Role FROM user_info WHERE User_Name = ?";
  $stmt = $db->prepare($current_user_query);
  $stmt->bind_param("s", $_SESSION['username']);
  $stmt->execute();
  $current_user_result = $stmt->get_result();
  $current_user_row = $current_user_result->fetch_assoc();
  $current_user_role = $current_user_row['Role'];
  $stmt->close();

  if( $current_user_role !== 'Superuser' &&  $current_user_role !== 'Admin') {
      header("Location: homepage.php");
      exit();
  }

  // Handle form submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // This is to prevent creating empty courses
    if( isset($_POST['course_name']) && empty($_POST['course_name']) ) {
      $_SESSION['error'] = 'Please enter valid course name.';
    } else if( isset($_POST['course_ID']) && empty($_POST['course_ID']) ) {
      $_SESSION['error'] = 'Please enter valid course ID.';
    } else if( isset($_POST['course_group']) && empty($_POST['course_group']) ) {
      $_SESSION['error'] = 'Please select course group.';
    } else if( !isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== 0 ) {
      $_SESSION['error'] = 'Please select a PDF file.';
    } else {
      $course_name = mysqli_real_escape_string($db, $_POST['course_name']);
      $course_ID = mysqli_real_escape_string($db, $_POST['course_ID']);
      $course_group = mysqli_real_escape_string($db, $_POST['course_group']);
      $sanitized_name = preg_replace('/[^A-Za-z0-9]/', '', $course_name);
      $upload_dir = "pdfs/";

      $file = $_FILES['pdf_file'];
      $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
      $new_file_name = $sanitized_name . "." . $file_extension;
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
          $query = "INSERT INTO courses (course_name, pdf_path, course_group, course_ID) VALUES (?, ?, ?, ?)";
          $stmt = $db->prepare($query);
          $stmt->bind_param("ssss", $course_name, $target_path, $course_group, $course_ID);
          
          if ($stmt->execute()) {
              $_SESSION['success'] = "Course created and PDF uploaded successfully!";
              // Also output success for AJAX detection
              echo '<div style="display:none;">Course created and PDF uploaded successfully!</div>';
          } else {
              $_SESSION['error'] = "Error creating course: " . $stmt->error;
          }
          $stmt->close();
      } else {
          $_SESSION['error'] = "Error uploading file.";
      }
    }
  }
?>

<html>
<head>
    <title>Learning Portal - Create Course</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/managecourses.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">Create New Course</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" role="alert">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <form action="createcourses.php" method="post" enctype="multipart/form-data" class="mb-4">
            <div class="form-group">
                <label>Course Name:</label>
                <input type="text" name="course_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Course ID:</label>
                <input type="text" name="course_ID" class="form-control" required>
            </div>
            <div class="form-group" style="display:flex; width: 100%;">
                <div style="flex: 50%;">
                    <label>Course Group:</label> </br>
                    <select name="course_group" class="form-control" required>
                        <option value="">--Select Group--</option>
                        <option value="MCAD">MCAD</option>
                        <option value="SLM">SLM</option>
                        <option value="PLM">PLM</option>
                        <option value="GDT">GDT</option>
                    </select>
                </div>
                <div style="flex: 50%; margin-left: 15px;">
                    <label>Upload PDF:</label>
                    <input type="file" name="pdf_file" accept=".pdf" class="form-control" id="pdf_file" required>
                    <div id="progress-container" style="display:none; width: 100%; background-color: #f3f3f3; border-radius: 10px; overflow: hidden; margin-top: 10px;">
                        <div id="progress-bar" style="width: 0%; height: 25px; background-color: #4caf50; transition: width 0.4s ease; border-radius: 10px;"></div>
                        <div id="progress-text" style="text-align: center; margin-top: 5px; font-weight: bold; color: #333;">0%</div>
                    </div>
                </div>
            </div>
            <div class="form-group" style="margin-top: 20px;">
                <button type="button" class="btn btn-custom" id="upload-button">Create Course</button>
                <button type="button" class="btn btn-custom"><a href="managecourses.php">Back to Manage Courses</a></button>
            </div>
        </form>
    </div>
    
    <script src="js/jquery-3.5.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    document.getElementById('upload-button').addEventListener('click', function(e) {
        e.preventDefault();
        const form = document.querySelector('form[action="createcourses.php"]');
        const formData = new FormData(form);

        // Validate form fields
        const courseName = form.querySelector('input[name="course_name"]').value;
        const courseID = form.querySelector('input[name="course_ID"]').value;
        const courseGroup = form.querySelector('select[name="course_group"]').value;
        const pdfFile = form.querySelector('input[name="pdf_file"]').files[0];

        if (!courseName || !courseID || !courseGroup || !pdfFile) {
            alert('Please fill in all fields and select a PDF file.');
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'createcourses.php', true);

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                document.getElementById('progress-container').style.display = 'block';
                document.getElementById('progress-bar').style.width = percentComplete + '%';
                document.getElementById('progress-text').textContent = percentComplete.toFixed(0) + '%';
            }
        });

        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('progress-container').style.display = 'none';
                // Parse response to check for success/error
                const response = xhr.responseText;
                if (response.includes('Course created and PDF uploaded successfully!')) {
                    // Show success message and reset form
                    const successDiv = document.createElement('div');
                    successDiv.className = 'alert alert-success';
                    successDiv.textContent = 'Course created and PDF uploaded successfully!';
                    document.querySelector('.container').insertBefore(successDiv, document.querySelector('form'));
                    
                    // Reset form
                    document.querySelector('form[action="createcourses.php"]').reset();
                    
                    // Hide message after 5 seconds
                    setTimeout(function() {
                        successDiv.style.transition = 'opacity 0.5s';
                        successDiv.style.opacity = '0';
                        setTimeout(function() {
                            successDiv.remove();
                        }, 500);
                    }, 5000);
                } else {
                    // Reload page to show error messages
                    window.location.reload();
                }
            } else {
                alert('An error occurred while creating the course.');
                document.getElementById('progress-container').style.display = 'none';
            }
        };

        xhr.onerror = function() {
            alert('An error occurred while creating the course.');
            document.getElementById('progress-container').style.display = 'none';
        };

        xhr.send(formData);
    });

    // Hide messages after 5 seconds
    function hideMessages() {
        const messages = document.querySelectorAll('.alert');
        messages.forEach(function(message) {
            message.style.transition = 'opacity 0.5s';
            message.style.opacity = '0';
            setTimeout(function() {
                message.style.display = 'none';
            }, 500);
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.querySelector('.alert-success');
        const errorMessage = document.querySelector('.alert-danger');
        if (successMessage || errorMessage) {
            setTimeout(hideMessages, 5000);
        }
    });
    </script>
</body>
</html>
