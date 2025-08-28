<?php
require 'server.php';


if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch the current user's ID and password
$user_query = "SELECT User_ID, Password FROM user_info WHERE User_Name = ?";
$stmt = $db->prepare($user_query);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    $_SESSION['error'] = "User not found!";
    header("Location: users.php");
    exit();
}

$user_id = $user['User_ID']; // Get the current user's ID
$current_password_hash = $user['Password']; // Get the current password

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the current password matches the stored password
    if ($current_password !== $current_password_hash) {
        $_SESSION['error'] = "⚠️ Current password is incorrect!";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "⚠️ New password and confirm password do not match!";
    } else {
        // Update the password in the database as plain text
        $update_query = "UPDATE user_info SET Password = ?, Confirm_Password = ? WHERE User_ID = ?";
        $stmt = $db->prepare($update_query);
        $stmt->bind_param("ssi", $new_password, $new_password, $user_id); // Store the plain text password
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Password changed successfully!";
            header("Location: users.php");
            exit();
        } else {
            $_SESSION['error'] = "⚠️ Error changing password!";
        }
    }
    // Redirect back to the same page to show error messages
    header("Location: changepass.php?id=" . $user_id);
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
    <h2>Change your Password</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="current_password">Current Password:</label>
        <input type="password" name="current_password" required>
        <br>
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