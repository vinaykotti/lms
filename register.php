<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <style>
  * {
  margin: 0px;
  padding: 0px;
}
body {
  font-size: 120%;
  background-image: url(images/BGLOGINBLUR.jpg);
  background-repeat: no-repeat;
  background-size: 100%;
 }

.header {
  width: 30%;
  margin: 50px auto 0px;
  color: white;
  background: #6DAF98;
  text-align: center;
  border: 1px solid #B0C4DE;
  border-bottom: none;
  border-radius: 10px 10px 0px 0px;
  padding: 20px;
}
form, .content {
  width: 30%;
  margin: 0px auto;
  padding: 20px;
  border: 1px solid #B0C4DE;
  background: white;
  border-radius: 0px 0px 10px 10px;
}
.input-group {
  margin: 10px 0px 10px 0px;

}
.input-group label {
  display: block;
  text-align: left;
  margin: 3px;
}
.input-group input {
  height: 30px;
  width: 93%;
  padding: 5px 10px;
  font-size: 16px;
  border-radius: 5px;
  border: 1px solid gray;
}
.btn {
  padding: 10px;
  font-size: 15px;
  color: white;
  background: #6DAF98;
  border: none;
  border-radius: 5px;
}
.error {
  width: 92%; 
  margin: 0px auto; 
  padding: 10px; 
  border: 1px solid #a94442; 
  color: #a94442; 
  background: #f2dede; 
  border-radius: 5px; 
  text-align: left;
}
.success {
  color: #3c763d; 
  background: #dff0d8; 
  border: 1px solid #3c763d;
  margin-bottom: 20px;
}
</style>
<Style>
	.bannername {	
	  display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
	  text-align: center;
      font-size: 32px; /* Adjust the font size as needed */
      font-family: Times New Roman;
	 
	}
	.bannername span {
		font-size: 45px;
		font-family: Algerian;
	}
	
</Style>
</head>
<body>
  <div class="header">
  	<h2>Create a new account</h2>
  </div>
  <form method="post" action="register.php">
  	<?php include('errors.php'); ?>
  	<div class="input-group">
  	  <label>User Name</label>
  	  <input type="text" name="username" value="<?php echo $username; ?>">
  	</div>
  	<div class="input-group">
  	  <label>First Name</label>
  	  <input type="text" name="firstname" value="<?php echo $firstname; ?>">
  	</div>
	<div class="input-group">
  	  <label>Last Name</label>
  	  <input type="text" name="lastname" value="<?php echo $lastname; ?>">
  	</div>
	<div class="input-group">
  	  <label>Authentication UID</label>
  	  <input type="text" name="uid" value="<?php echo $uid; ?>">
  	</div>
  	<div class="input-group">
  	  <label>Password</label>
  	  <input type="password" name="password">
  	</div>
  	<div class="input-group">
  	  <label>Confirm password</label>
  	  <input type="password" name="confirmpassword">
  	</div class="input-group">
	<div>
	  <label >Please Select Your Role</label>
 
             <select name="role">
			          <option name=""  value="">--Select Role--</option>   
                      <option>Admin</option>
                      <option>Trainer</option>
                      <option>Trainee</option>
                      <?php
	                    $query=mysqli_query($db, "select * from user_info where User_Name='".$_SESSION['username']."'");
	                        while($row1=mysqli_fetch_array($query)){
		                      $role=$row1['Role'];}
                              if($role=='Superuser'){					?>
					 <option>Superuser</option>
					 <?php }
							else
							{ ?>
							<?php }
							?>    
             </select>
	</div>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="reg_user">Submit</button>
  	</div>
  </form>
</body>
</html>