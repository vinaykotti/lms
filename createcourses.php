<!DOCTYPE html>
<?php include "server.php"; ?> 
<?php
  // This is to prevent creating empty courses
  if( isset($_POST['course_name']) && empty($_POST['course_name']) ) {
    echo '<div class="alert alert-danger" role="alert">Please enter valid course name.</div>';
    exit();
  }
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
?>


<html>
<head>
    <title>Learning Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/managecourses.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">Manage Courses</h1>
        <form action="pdfupload.php" method="post" enctype="multipart/form-data" class="mb-4">
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
                    <select name="course_group" required>
                        <option value="">--Select Group--</option>
                        <option value="MCAD">MCAD</option>
                        <option value="SLM">SLM</option>
                        <option value="PLM">PLM</option>
                        <option value="GDT">GDT</option>
                    </select>
                </div>
                <div style="flex: 50%;">
                    <label>Upload PDF:</label>
                    <input type="file" name="pdf_file" accept=".pdf" class="form-control" id="pdf_file" required>
                    <div id="progress-container" style="display:none; width: 100%; background-color: #f3f3f3; border-radius: 10px; overflow: hidden;">
                        <div id="progress-bar" style="width: 0%; height: 25px; background-color: #4caf50; transition: width 0.4s ease; border-radius: 10px;"></div>
                        <div id="progress-text" style="text-align: center; margin-top: 5px; font-weight: bold; color: #333;">0%</div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-custom" id="upload-button">Upload</button>
        </form>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="js/jquery-3.5.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    document.getElementById('upload-button').addEventListener('click', function(e) {
        e.preventDefault();
        const form = document.querySelector('form[action="pdfupload.php"]');
        const formData = new FormData(form);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'pdfupload.php', true);

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
                alert('File uploaded successfully!');
                document.getElementById('progress-container').style.display = 'none';
            } else {
                alert('An error occurred while uploading the file.');
            }
        };

        xhr.send(formData);
    });

    // Hide messages after 5 seconds
    function hideMessages() {
        const messages = document.querySelectorAll('.message');
        messages.forEach(function(message) {
            message.style.transition = 'opacity 0.5s';
            message.style.opacity = '0';
            setTimeout(function() {
                message.style.display = 'none';
            }, 500);
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.querySelector('.success');
        if (successMessage) {
            setTimeout(hideMessages, 5000);
        }
    });
    </script>
</body>
</html>