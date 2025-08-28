<!DOCTYPE html>
<?php include "server.php"; ?> 
<html>
<head>
    <title>Learning Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/user.css">
<style>
    .del {
    background-color: #ff4444;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 3px;
}

.del:hover {
    background-color: #cc0000;
}

.message {
    padding: 10px;
    margin: 10px 0;
    border-radius: 4px;
    opacity: 1;
    transition: opacity 0.5s ease;
}

.success {
    background-color: #dff0d8;
    border: 1px solid #d6e9c6;
    color: #3c763d;
}

.error {
    background-color: #f2dede;
    border: 1px solid #ebccd1;
    color: #a94442;
}

.change-password {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 3px;
}

.change-password:hover {
    background-color: #0056b3;
}
</style>
    
	
</head>
<body>
    <h1 class="text-center">MANAGE USERS</h1>
<?php if(isset($_SESSION['success'])): ?>
    <div class="message success">
        <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
        ?>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="message error">
        <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>
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

    <div class="w3-ul"  >
	<table border="1" style="width:100%; background-color:#FFFFE0;border-color:brown;font-size:18px;">
        <tr>
            <th>User ID</th>
            <th>First Name</th>
            <th>Last Name</th>
			<th>User Name</th>
			<th>Role</th>
			<th>Created On</th>
            <?php 
            // Check if the user is Admin or Superuser before showing the delete button
            if ($current_user_role == 'Superuser') {
                echo "<th colspan='2'>Action</th>";
            }
            ?>
            			
            <!-- Add more column headers as needed -->
            <?php if ($current_user_role === 'Superuser' || $current_user_role === 'Admin'): ?>
                <th>Password</th> <!-- Show password column for Superuser -->
            <?php endif; ?>
        </tr>
        <?php
        if(!isset($_SESSION['username'])) { 
            header("Location: login.php");
            exit();
        }

      

        // Fetch all users
        $select_query = "SELECT * FROM user_info";
        $run_query = mysqli_query($db, $select_query) or die(mysqli_error($db));
        while($row = $run_query->fetch_assoc()) {
            // Don't show delete button for the current admin
            $show_delete = ($row['User_Name'] !== $_SESSION['username']);
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['User_ID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['First_Name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['User_Name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Role']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Date_time']) . "</td>";
              // Check if the user is Admin or Superuser before showing the delete button
              if ($current_user_role == 'Superuser') {
                echo "<td><a href='deleteuser.php?id=" . $row['User_ID'] . "' 
                        onclick='return confirm(\"Are you sure you want to delete this user? This action cannot be undone.\");'>
                        <button class='del'>DELETE</button>
                      </a></td>";
                      
            } 
            
            // Check if the user is Admin or Superuser before showing the delete button
            if ($current_user_role == 'Superuser') {
                echo "<td><a href='updatepassword.php?id=" . $row['User_ID'] . "'>
                    <button class='change-password'>CHANGE PASSWORD</button>
                  </a></td>";
            }
            if ($current_user_role === 'Superuser' || $current_user_role === 'Admin'):
               echo "<td>
                    <span id='password_" . $row['User_ID'] . "' style='display:none'>" . htmlspecialchars($row['Password']) . "</span>
                    <button type='button' onclick='togglePassword(" . $row['User_ID'] . ")' id='showBtn_" . $row['User_ID'] . "'>See</button>
                    </td>";
            endif; 
            
            
            echo "</tr>";
        }
        ?>
    </table>
	</div>

<script>
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

// If there's a success message, hide it after 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.querySelector('.success');
    if (successMessage) {
        setTimeout(hideMessages, 3000);
    }
});
function togglePassword(userId) {
    const passwordSpan = document.getElementById('password_' + userId);
    const showBtn = document.getElementById('showBtn_' + userId);
    
    if (passwordSpan.style.display === 'none') {
        passwordSpan.style.display = 'inline';
        showBtn.textContent = 'Hide';
    } else {
        passwordSpan.style.display = 'none';
        showBtn.textContent = 'See';
    }
}

 
</script>
</body>
</html>