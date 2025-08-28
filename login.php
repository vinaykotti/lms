<!DOCTYPE html>
<?php include "server.php"; ?> 
<html>
<head>
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/login.css">
</head>
<body class="loginbody">
<form method="post" action="login.php"  class="loginform">
  <nav>AUTHENTICATION</nav>
 <?php include('errors.php'); ?>
  <div class="login_container">
    <label><b>Username</b></label>    
    <input type="text" placeholder="Enter Username" name="username"class="login_input">
    <label><b>Password</b></label>
    <input type="password" placeholder="Enter Password" id="password" name="password" class="login_input">
    <input type="checkbox"  onclick="showpwd()">Show Password
    <div class="buttondiv"> <button type="submit" name="login_user" class="submitbutton" >Log-In</button></div>
  </div>

</form>
<script>
function showpwd() {
  var x = document.getElementById("password");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
</script>
</body>
</html>