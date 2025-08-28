<!-- header.php -->
 <link rel="stylesheet" href="css/header.css">
<script src="js/header.js"></script>
<header class="topbar">
  <div class="logo">
    <h2>LEARNING PORTAL</h2>
  </div>

  <div class="user-menu">
    <div class="user-info" onclick="toggleDropdown()">
      <img src="images/avatar.png" alt="User Avatar" class="avatar">
      <span><?php  
                	$stmt = $db->prepare("SELECT CONCAT(First_Name, ' ', Last_Name) AS FullName, Role FROM user_info WHERE User_Name = ?");
                    $stmt->bind_param("s", $_SESSION['username']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                        while ($rows = $result->fetch_assoc()) {
                            echo htmlspecialchars($rows['FullName']) . "<br>";
                            echo htmlspecialchars($rows['Role']);}
                    $stmt->close(); ?>
</span>
      <span class="arrow">▼</span>
    </div>
    <div id="dropdown" class="dropdown-content">
        <a href="homepage.php"><i class="icon">🏠</i> Home</a>
      <a href="dashboard.php"><i class="icon">📊</i> Dashboard</a>
      <a href="changepassword.php"><i class="icon">🔒</i> Change Password</a>
      <a href="logout.php"><i class="icon">⏻</i> Logout</a>
    </div>
  </div>
</header>
