<?php
require 'server.php';

session_start(); // Ensure the session is started

if (!isset($_SESSION['username'])) { 
    header("Location: login.php");
    exit();
}

// Check if current user is admin
$admin_check = $db->prepare("SELECT Role FROM user_info WHERE User_Name = ?");
$admin_check->bind_param("s", $_SESSION['username']);
$admin_check->execute();
$admin_result = $admin_check->get_result();
$user_role = $admin_result->fetch_assoc()['Role'];
$admin_check->close();

if ($user_role !== 'Superuser') {
    header("Location: homepage.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    try {
        // Start transaction
        $db->begin_transaction();
        
        // Get user info before deletion
        $user_query = "SELECT User_Name FROM user_info WHERE User_ID = ?";
        $stmt = $db->prepare($user_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        $user = $user_result->fetch_assoc();
        
        if ($user) {
            // Prevent deleting the current admin
            if ($user['User_Name'] === $_SESSION['username']) {
                throw new Exception("Cannot delete your own admin account!");
            }
            
            // Delete from course_access if the table exists
            $check_table = $db->query("SHOW TABLES LIKE 'course_access'");
            if ($check_table->num_rows > 0) {
                $delete_access = "DELETE FROM course_access WHERE user_id = ?";
                $stmt = $db->prepare($delete_access);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
            }
            
            // Delete from user_login_history if the table exists
            $check_table = $db->query("SHOW TABLES LIKE 'user_login_history'");
            if ($check_table->num_rows > 0) {
                $delete_history = "DELETE FROM user_login_history WHERE User_Name = ?";
                $stmt = $db->prepare($delete_history);
                $stmt->bind_param("s", $user['User_Name']);
                $stmt->execute();
            }
            
            // Finally delete the user
            $delete_user = "DELETE FROM user_info WHERE User_ID = ?";
            $stmt = $db->prepare($delete_user);
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                $db->commit();
                $_SESSION['success'] = "User deleted successfully!";
            } else {
                throw new Exception("Failed to delete user!");
            }
            
        } else {
            throw new Exception("User not found!");
        }
        
    } catch (Exception $e) {
        $db->rollback();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
} else {
    $_SESSION['error'] = "Invalid request!";
}

// Redirect to users.php after processing
header("Location: users.php");
exit();
?>

<?php if(isset($_SESSION['message'])): ?>
    <div class="message <?php echo strpos($_SESSION['message'], 'Error') !== false ? 'error' : 'success'; ?>">
        <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message']);
        ?>
    </div>
<?php endif; ?> 