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
<link rel="stylesheet" href="css/courses.css">


</head>
<body>
    <?php include "header.php"; ?>
  <div style="text-align:center;"><h2>Select Course Module</h2></div>
        <table> 
            <tr>
                <td style="white-space: nowrap; padding:10px;"><a href="courses.php?group=MCAD"> <button class="button-m"> MCAD </button></a></td>
                <td style="white-space: nowrap; padding:10px;"><a href="courses.php?group=SLM"> <button class="button-m"> SLM </button></a></td>
                <td style="white-space: nowrap; padding:10px;"><a href="courses.php?group=PLM"> <button class="button-m"> PLM </button></a></td>
                <td style="white-space: nowrap; padding:10px;"><a href="gdt.php"> <button class="button-m"> GD&T </button></a></td>
                <td style="white-space: wrap; padding:10px;"><a href="aftereffects/aftereffects.php"> <button class="button-m"> AFTER EFFECTS</button></a></td>
            </tr>
        </table></div>
<script src="js/button.js"></script>
</body>
</html>