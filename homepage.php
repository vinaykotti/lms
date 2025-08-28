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
<link rel="stylesheet" href="css/homepage.css">
</head>
<body>
    <?php include "header.php"; ?>
  <div class="content">
      <div style="width:100%; font-size:200px;"><h1>Welcome to Adroitec Learning Portal</h1></div>
      <div style="text-align:right;"> <a href="modules.php"><button style="width:200px; height:25px;">Continue to Courses</button> </a> </div>
    </div>
</div>
</body>
</html>