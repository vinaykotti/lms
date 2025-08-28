<?php include "server.php"; ?>
 <?php  
 
if(!$_SESSION['username'])  
{ 
  header("Location: login.php");
}  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/viewer.css">
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
  

 </head>
 
<body>
<div id="pdf-interface">
<div id="toolbar">
		<div><?php  
			$stmt = $db->prepare("SELECT CONCAT(First_Name, ' ', Last_Name) AS FullName, Role FROM user_info WHERE User_Name = ?");
			$stmt->bind_param("s", $_SESSION['username']);
			$stmt->execute();
			$result = $stmt->get_result();
			while ($rows = $result->fetch_assoc()) {
			echo "<strong>Username:</strong> " . htmlspecialchars($rows['FullName']) . "<br>";
			echo "<strong>Role:</strong> " . htmlspecialchars($rows['Role']);
				}	
			$stmt->close();
			?></div>
		<div><a href="courses.php"><button style="width:100px; height:25px;">BACK</button></a>
		<a href="logout.php"><button style="width:100px; height:25px;">LOGOUT</button> </a> </div>
	<!--	<h1> Introduction to Creo Parametric 5.0 Fundamentals </h1> -->
		<div><input type="text" id="search-input" placeholder="Search text..." />
             <button id="search-btn">Search</button></div>
        <div><button id="zoom-in">Zoom In</button>
             <button id="zoom-out">Zoom Out</button> </div>
		<div><button id="prev-btn" disabled>Previous</button>
        <button id="next-btn" disabled>Next</button></div>
		<div><button id="showDropdownBtn"> Videos </button>
			 <a id="zipLink" onclick="downloadzip()" download><button> Working Files</button></a> </div>
		<div><select id="fileDropdown">
			 <option value="">-- Select Video --</option></select></div>
</div>
		<div id="pdf-container"></div>
<div id="bookmarks"> TOPICS </div>	
<div id="bookoverlay"><button id="add-bookmark">Add Bookmark</button></div>
 <div id="overlay"></div>
    <div id="videoModal">
          <video id="videoPlayer" controls playsinline controlsList="nodownload" ></video>
    <button id="closeModal">Close</button>
    </div>  
    <script src="js/viewer.js"></script>
</body>
</html>
