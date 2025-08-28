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
?>
<?php
                // Fetch all courses from the database
        $query = "SELECT * FROM courses";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
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
         <!-- Group Selection for Filtering Courses -->
    <div>
        <h4>Filter Courses by Group</h4>
        <form method="GET" action="">
            <label for="filter_group">Select Group:</label>
            <select name="filter_group" id="filter_group">
                <option value="" <?php echo !isset($_GET['filter_group']) || $_GET['filter_group'] === '' ? 'selected' : ''; ?>>--All Groups--</option>
                <option value="MCAD" <?php echo isset($_GET['filter_group']) && $_GET['filter_group'] === 'MCAD' ? 'selected' : ''; ?>>MCAD</option>
                <option value="SLM" <?php echo isset($_GET['filter_group']) && $_GET['filter_group'] === 'SLM' ? 'selected' : ''; ?>>SLM</option>
                <option value="PLM" <?php echo isset($_GET['filter_group']) && $_GET['filter_group'] === 'PLM' ? 'selected' : ''; ?>>PLM</option>
                <option value="GDT" <?php echo isset($_GET['filter_group']) && $_GET['filter_group'] === 'GDT' ? 'selected' : ''; ?>>GDT</option>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>
          
           
<div style="display: flex; justify-content: space-between;  width: 50%;" class="course-table">
               <span style='text-align: center; flex: 20%;'> Course ID </span>
               <span style='text-align: left; flex: 60%;'> Course name </span>
               <span style='text-align: center; flex: 20%;'> Authorization </span>
    </div>

                <?php
// Fetch courses from database based on selected group
$filter_group = isset($_GET['filter_group']) ? $_GET['filter_group'] : '';
$query = "SELECT * FROM courses";
if ($filter_group) {
    $query .= " WHERE course_group = ?";
}
$stmt = $db->prepare($query);
if ($filter_group) {
    $stmt->bind_param("s", $filter_group);
}
$stmt->execute();
$result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        

                        echo "<div class='course-row'>";
                        echo "<div class='course-info' style='display: flex; justify-content: space-between;  width: 50%;'>";
                        echo "<span style='text-align: center; flex: 20%;'>" . htmlspecialchars($row['course_ID']) . "</span>";
                        echo "<span style='flex: 60%;'>" . htmlspecialchars($row['course_name']) . "</span>";
                        echo "<span style='flex: 20%;'><button type='button' onclick='toggleMoreInfo(" . htmlspecialchars($row['id']) . ")' class='btn btn-custom'>More Info</button></span>";
                        echo "</div>";
                        echo "<div id='more_info_" . htmlspecialchars($row['id']) . "' class='more-info' style='width:50%; display: none;  padding: 0px 10px;   transition: width 2s;'>"; // More info div
                        
                        // User access control cell
                        echo "<form action='update_access.php' method='post' style='flex: 50%;'>";
                        echo "<input type='hidden' name='course_id' value='" . htmlspecialchars($row['id']) . "'>";
                        
                        // Fetch all users
                        $users_query = "SELECT User_ID, User_Name, First_Name, Last_Name FROM user_info ORDER BY First_Name";
                        $users_result = $db->query($users_query);
                        
                        // Fetch current access for this course
                        $access_query = "SELECT user_id FROM course_access WHERE course_id = " . $row['id'];
                        $access_result = $db->query($access_query);
                        $authorized_users = [];
                        while($access = $access_result->fetch_assoc()) {
                            $authorized_users[] = $access['user_id'];
                        }
                        
                        echo "<div class='user-list-container' style='max-height: 150px; overflow-y: auto; display: flex; flex-direction: column; gap: 5px; padding: 0px 15px 15px 15px; background: rgba(255,255,255,0.9); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
                        while($user = $users_result->fetch_assoc()) {
                            $checked = in_array($user['User_ID'], $authorized_users) ? 'checked' : '';
                            echo "<div class='user-item' style='display: flex; align-items: center; padding: 8px; border-radius: 4px; transition: all 0.2s; background: white; border: 1px solid #eee;'>";
                            echo "<input type='checkbox' name='users[]' value='" . $user['User_ID'] . "' $checked style='margin-right: 12px; width: 18px; height: 18px; cursor: pointer;'>";
                            echo "<span style='flex-grow: 1; font-size: 14px;'>" . htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']) . "</span>";
                            echo "</div>";
                        }
                        echo "</div>";
                        echo "<input type='submit' value='Update Access' class='btn btn-primary' style='margin-top: 15px; padding: 8px 20px; border-radius: 20px; background: #007bff; border: none; color: white; cursor: pointer; transition: all 0.2s;'>";
                        echo "</form>";

                        echo "<div class='action-buttons' style='flex: 50%; margin: 0px 20px 0px 20px; display: flex; gap: 5px; flex-wrap: wrap; align-items: center;'>";
                        
                        // PDF Upload Form
                        echo "<form action='updatecourse.php' method='post' enctype='multipart/form-data' style='display:inline;'>";
                        echo "<input type='hidden' name='course_id' value='" . htmlspecialchars($row['id']) . "'>";
                        echo "<input type='file' name='new_pdf' accept='.pdf' style='display:none;' id='pdf_" . htmlspecialchars($row['id']) . "'>";
                        echo "<button type='button' onclick='document.getElementById(\"pdf_" . htmlspecialchars($row['id']) . "\").click();' class='action-btn pdf-btn' style=' align-items: center; padding: 4px 10px; background: #4da6ff; border: none; border-radius: 25px; color: white; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                                <img src='images/—Pngtree—pdf file icon png_7965915.png' alt='PDF' style='width: 20px; height: 20px; margin-right: 8px;'> Update PDF
                              </button>
                              <button type='submit' id='submit_pdf_" . htmlspecialchars($row['id']) . "' style='display:none;' class='btn btn-custom'>Upload PDF</button>
                        </form>";

                        // Video Upload Form
                        echo "<form action='uploadvideo.php' method='post' enctype='multipart/form-data' style='display:inline;'>";
                        echo "<input type='hidden' name='MAX_FILE_SIZE' value='2147483648'>";
                        echo "<input type='hidden' name='course_id' value='" . htmlspecialchars($row['id']) . "'>";
                        echo "<input type='file' name='video_files[]' accept='video/*' multiple style='display:none;' id='video_" . htmlspecialchars($row['id']) . "'>";
                        echo "<button type='button' onclick='document.getElementById(\"video_" . htmlspecialchars($row['id']) . "\").click();' class='action-btn video-btn' style=' align-items: center; padding: 8px 15px; background: #66cc66; border: none; border-radius: 25px; color: white; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                                <img src='images/play button.png' alt='Video' style='width: 20px; height: 20px; margin-right: 8px;'> Add Videos
                              </button>
                              <button type='submit' id='submit_video_" . htmlspecialchars($row['id']) . "' style='display:none;' class='btn btn-custom'>Upload Videos</button>
                        </form>";

                        // ZIP Upload Form  
                        echo "<form action='uploadzip.php' method='post' enctype='multipart/form-data' style='display:inline;'>";
                        echo "<input type='hidden' name='course_id' value='" . htmlspecialchars($row['id']) . "'>";
                        echo "<input type='file' name='zip_file' accept='.zip' style='display:none;' id='zip_" . htmlspecialchars($row['id']) . "'>";
                        echo "<button type='button' onclick='document.getElementById(\"zip_" . htmlspecialchars($row['id']) . "\").click();' class='action-btn zip-btn' style=' align-items: center; padding: 8px 15px; background: #ffcc66; border: none; border-radius: 25px; color: white; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                                <img src='images/file-zip-icon.png' alt='ZIP' style='width: 20px; height: 20px; margin-right: 8px;'> Add ZIP
                              </button>
                              <button type='submit' id='submit_zip_" . htmlspecialchars($row['id']) . "' style='display:none;' class='btn btn-custom'>Upload ZIP</button>
                        </form>";

                        if ($current_user_role == 'Admin' || $current_user_role == 'Superuser') {
                            echo "<a href='deletecourse.php?id=" . htmlspecialchars($row['id']) . "' 
                                    onclick='return confirm(\"Are you sure you want to delete this course?\")'>
                                    <button class='action-btn delete-btn' style='padding: 8px 15px; background: #dc3545; border: none; border-radius: 25px; color: white; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>Delete</button>
                                  </a>";
                        }

                        echo "</div>"; // Close action buttons div
                        echo "</div>"; // Close more info div
                        echo "</div>"; // Close course row div
                    }
                } else {
                    echo "<div style='text-align: center;'>No courses found</div>";
                }
                ?>
            
       
    </div>
    <script src="js/jquery-3.5.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const submitBtn = document.getElementById('submit_' + this.id.split('_')[1]);
                    submitBtn.style.display = 'inline';
                }
            });
        });

        // Handle PDF file input
        const pdfInputs = document.querySelectorAll('input[name="new_pdf"]');
        pdfInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const submitBtn = document.getElementById('submit_pdf_' + this.id.split('_')[1]);
                    submitBtn.style.display = 'inline';
                }
            });
        });

        // Handle video file input
        const videoInputs = document.querySelectorAll('input[name="video_files[]"]');
        videoInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const submitBtn = document.getElementById('submit_video_' + this.id.split('_')[1]);
                    submitBtn.style.display = 'inline';
                }
            });
        });

        // Handle ZIP file input
        const zipInputs = document.querySelectorAll('input[name="zip_file"]');
        zipInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const submitBtn = document.getElementById('submit_zip_' + this.id.split('_')[1]);
                    submitBtn.style.display = 'inline';
                }
            });
        });
    });

    function toggleEdit(id) {
        // Hide display, show input
        document.getElementById('display_' + id).style.display = 'none';
        document.getElementById('name_' + id).style.display = 'inline';
        document.getElementById('name_' + id).focus();
        
        // Hide edit button, show save and cancel
        document.getElementById('edit_btn_' + id).style.display = 'none';
        document.getElementById('save_' + id).style.display = 'inline';
        document.getElementById('cancel_' + id).style.display = 'inline';
        
        // Store original value for cancel
        document.getElementById('name_' + id).dataset.original = 
            document.getElementById('name_' + id).value;
    }

    function cancelEdit(id) {
        // Restore original value
        const input = document.getElementById('name_' + id);
        input.value = input.dataset.original;
        
        // Show display, hide input
        document.getElementById('display_' + id).style.display = 'inline';
        input.style.display = 'none';
        
        // Show edit button, hide save and cancel
        document.getElementById('edit_btn_' + id).style.display = 'inline';
        document.getElementById('save_' + id).style.display = 'none';
        document.getElementById('cancel_' + id).style.display = 'none';
    }

    // Function to hide messages
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

    // If there's a success message, hide it after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.querySelector('.success');
        if (successMessage) {
            setTimeout(hideMessages, 5000); // 5000 milliseconds = 5 seconds
        }
    });

    function toggleAuthorizedUsers(courseId) {
        const usersDiv = document.getElementById('authorized_users_' + courseId);
        if (usersDiv.style.display === 'none') {
            usersDiv.style.display = 'flex'; // Show the users
        } else {
            usersDiv.style.display = 'none'; // Hide the users
        }
    }

    document.getElementById('upload-button').addEventListener('click', function(e) {
        e.preventDefault();               // stop the normal double-submit
        const form = document.querySelector('form[action="pdfupload.php"]');
        const formData = new FormData(form);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'pdfupload.php', true);

        // Update progress bar
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
                // Handle success
                alert('File uploaded successfully!');
                document.getElementById('progress-container').style.display = 'none';
            } else {
                // Handle error
                alert('An error occurred while uploading the file.');
            }
        };

        xhr.send(formData);
    });

    function toggleMoreInfo(courseId) {
        const moreInfoDiv = document.getElementById('more_info_' + courseId);
        if (moreInfoDiv.style.display === 'none') {
            moreInfoDiv.style.display = 'flex'; // Show the more info div
        } else {
            moreInfoDiv.style.display = 'none'; // Hide the more info div
        }
    }
    </script>

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

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ensure you have a connection to the database
        // Assuming $db is your database connection

        // Get the course name from the form
        $course_name = $_POST['course_name'];

        // Prepare the SQL statement to insert the course name
        $insert_query = "INSERT INTO courses (course_name) VALUES (?)";
        $stmt = $db->prepare($insert_query);
        $stmt->bind_param("s", $course_name);

        // Execute the statement
        if ($stmt->execute()) {
            // Success message or redirect
            $_SESSION['success'] = "Course added successfully!";
        } else {
            // Error handling
            $_SESSION['error'] = "Error adding course: " . $stmt->error;
        }
        $stmt->close();
    }
    ?>
</body>
</html>