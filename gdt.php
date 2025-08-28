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
<link rel="stylesheet" href="gdt/gdt.css">
</head>
<body>
<?php include "header.php"; ?>
<div class="sidebar">
    
    <a  href="gdt/courses/1.php" target="contentFrame" >Advanced GD&T ASME Y14.5 - 2009 (Includes Y14.5M -1994)</a>
    <a  href="gdt/courses/2.php" target="contentFrame" >Fundamentals of GD&T ASME Y14.5-2009</a>
    <a  href="gdt/courses/3.php" target="contentFrame" >Fundamentals of GD&T ASME Y14.5-2018</a>
    <a  href="gdt/courses/4.php" target="contentFrame" >Fundamentals of GD&T ASME Y14.5M-1994</a>
    <a  href="gdt/courses/5.php" target="contentFrame" >GD&T ASME Y14.5 2009 from 1994 Update Course</a>
    <a  href="gdt/courses/6.php" target="contentFrame" >comming Soon ....</a>
</div>
<div class="container"> 
    <h1> GD&T Training Bundle</h1>
  <div class= "content">
        <iframe name="contentFrame" src="infotiles.php" style="width:100%; height:85vh; border:none;"></iframe>
    </div>
   </div> 
<script src="js/button.js"></script>
                    <script>
function loadCourse(directory, targetPage) {
    // Fetch chapters for the selected directory
    fetch(`gdt/courses/get_chapters.php?dir=${encodeURIComponent(directory)}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                // Store the chapters data in localStorage
                localStorage.setItem('chapters', JSON.stringify(data));

                // Redirect to the respective course page
                window.location.href = targetPage;
            }
        })
        .catch(error => console.error('Error:', error));
}
</script>
</body>
</html>
