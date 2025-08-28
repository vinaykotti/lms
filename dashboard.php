<?php include "server.php"; ?>
<?php  
if(!$_SESSION['username'])  
{ 
  header("Location: login.php");
}  
?>
<html>
<head>
<title>Learning Portal</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/dash.css">
</head>
<body>
    <?php include "header.php"; ?>
    <div class="sidebar">
    <?php
	$query=mysqli_query($db, "select * from user_info where User_Name='".$_SESSION['username']."'");
	while($row1=mysqli_fetch_array($query)){
	$role=$row1['Role'];}
    if($role=='Admin' || $role=='Superuser' || $role=='Trainer'){					
    ?>
    <a href="createcourses.php" target="contentFrame" >Create Courses</a>
    <?php }
			else
				{ ?>
				<?php }
				?>
    <?php
	$query=mysqli_query($db, "select * from user_info where User_Name='".$_SESSION['username']."'");
	while($row1=mysqli_fetch_array($query)){
	$role=$row1['Role'];}
    if($role=='Admin' || $role=='Superuser' || $role=='Trainer'){					
    ?>
    <a href="managecourses.php" target="contentFrame" >Manage Courses</a>
    <?php }
			else
				{ ?>
				<?php }
				?>
    <?php
	$query=mysqli_query($db, "select * from user_info where User_Name='".$_SESSION['username']."'");
	    while($row1=mysqli_fetch_array($query)){
		$role=$row1['Role'];}
        if($role=='Admin' || $role=='Superuser'){					
    ?>
    <a  href="users.php" target="contentFrame" >Manage Users</a>
    <?php }
			else
				{ ?>
				<?php }
				?>
    
</div>
<div class="content">
  <iframe name="contentFrame" src="infotiles.php" style="width:100%; height:85vh; border:none;"></iframe>
</div>

</body>
</html>