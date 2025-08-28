<?php

session_start();

// Variable initialization
$username = "";
$firstname    = "";
$lastname  = "";
$password = "";
$confirmpassword = "";
$uid = "";
$Authen_id = "12924275545";
$errors = array();
$currentuser = "$username";

// connecting to the database
$db = mysqli_connect('localhost', 'root', 'vinswe123', 'lms_db');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receiving all input values
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
  $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  $confirmpassword = mysqli_real_escape_string($db, $_POST['confirmpassword']);
  $uid = mysqli_real_escape_string($db, $_POST['uid']);
  $role = mysqli_real_escape_string($db, $_POST['role']);
  // validation
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($firstname)) { array_push($errors, "First name is required"); }
  if (empty($lastname)) { array_push($errors, "Last name is required"); }
  if (empty($password)) { array_push($errors, "Password is required"); }
  if ($password != $confirmpassword) {array_push($errors, "Passwords do not match");  }
  if (empty($uid)) { array_push($errors, "Authentication UID is required"); }
  if ($uid != $Authen_id) {	array_push($errors, "Authentication UID is incorrect"); }
  if (empty($role)) { array_push($errors, "Role is required"); }

  // check availability whether the user already exists with the same username
  $user_check_query = "SELECT * FROM user_info WHERE User_Name='$username' LIMIT 1"; /* JVM - edit this line for table name and user name field as per the tabel and respective column */
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['User_Name'] === $username) {
      array_push($errors, "Username already exists");
    }
  }

  // Register user if there are no errors in the form
  if (count($errors) == 0) {
  	//$password = md5($password);//encrypt the password before saving in the database

  	$query = "INSERT INTO user_info (User_Name, First_Name, Last_Name, Password, Confirm_Password, Role) 
  			  VALUES('$username', '$firstname', '$lastname', '$password', '$confirmpassword', '$role')"; /* JVM - Change table name as per database. Ensure form names are same as table column names */
  	mysqli_query($db, $query);
  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
}
# LOGIN USER
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
  //	$password = md5($password);
  	$query = "SELECT * FROM user_info WHERE User_Name='$username' AND Password='$password'";/* JVM - Change table name as per database. */
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['username'] = $username; 
  	  $_SESSION['success'] = "You are now logged in";
  	header('location: homepage.php');
	

  	}else {
  		array_push($errors, "Incorrect Username or Password");
  	}
  }
}

# RECORD LOGIN TIME DATA
if (isset($_POST['login_time'])){
	$username = mysqli_real_escape_string($db, $_POST['username']);
	$username = $_SESSION['username']; // Assuming you store the user ID in a session variable
    $login_time = date('Y-m-d H:i:s');
    $query = "INSERT INTO user_login_history (User_Name, login_time) VALUES ($username, '$login_time')";

}

?>