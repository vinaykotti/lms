<?php
require 'server.php';


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
    
    // Fetch user information
    $user_query = "SELECT User_Name FROM user_info WHERE User_ID = ?";
    $stmt = $db->prepare($user_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user = $user_result->fetch_assoc();
    
    if (!$user) {
        $_SESSION['error'] = "User not found!";
        header("Location: users.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request!";
    header("Location: users.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
    } else {
        // Update the password in the database as plain text
        $update_query = "UPDATE user_info SET Password = ?, Confirm_Password = ? WHERE User_ID = ?";
        $stmt = $db->prepare($update_query);
        $stmt->bind_param("ssi", $new_password, $new_password, $user_id); // Store the plain text password
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Password changed successfully!";
        } else {
            $_SESSION['error'] = "Error changing password!";
        }
    }
    header("Location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/changepassword.css">
</head>
<body>
    <h2>Change Password for <?php echo htmlspecialchars($user['User_Name']); ?></h2>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required>
        <br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required>
        <br>
        <button type="submit">Change Password</button>
    </form>
    <a href="users.php">Cancel</a>
</body>
</html> 