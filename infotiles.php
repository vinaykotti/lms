<?php include "server.php"; ?>
 <?php  
 
if(!$_SESSION['username'])  
{ 
  header("Location: login.php");
}  
?>
<?php
  // Fetch the current user's role
  $current_user_query = "SELECT Role FROM user_info WHERE User_Name = ?";
  $stmt = $db->prepare($current_user_query);
  $stmt->bind_param("s", $_SESSION['username']);
  $stmt->execute();
  $current_user_result = $stmt->get_result();
  $current_user_row = $current_user_result->fetch_assoc();
  $current_user_role = $current_user_row['Role'];
  $stmt->close();

  if( $current_user_role !== 'Superuser' &&  $current_user_role !== 'Admin') {
      header("Location: noaccess.php");
      exit();
  }

  // Fetch the total number of users by role
  $role_counts_query = "SELECT Role, COUNT(*) AS count FROM user_info GROUP BY Role";
  $role_counts_result = $db->query($role_counts_query);

  // Initialize counts
  $super_users_count = 0;
  $admins_count = 0;
  $trainers_count = 0;
  $trainees_count = 0;
// Process the results
  while ($row = $role_counts_result->fetch_assoc()) {
      switch ($row['Role']) {
          case 'Superuser':
              $super_users_count = $row['count'];
              break;
          case 'Admin':
              $admins_count = $row['count'];
              break;
          case 'Trainer':
              $trainers_count = $row['count'];
              break;
          case 'Trainee':
              $trainees_count = $row['count'];
              break;
      }
  }

  // Fetch the total number of users by role
  $group_counts_query = "SELECT course_group, COUNT(*) AS count FROM courses GROUP BY course_group";
  $group_counts_result = $db->query($group_counts_query);

  // Initialize counts
  $MCAD = 0;
  $SLM = 0;
  $PLM = 0;
  $GDT = 0;
// Process the results
  while ($row = $group_counts_result->fetch_assoc()) {
      switch ($row['course_group']) {
          case 'MCAD':
              $MCAD = $row['count'];
              break;
          case 'SLM':
              $SLM = $row['count'];
              break;
          case 'PLM':
              $PLM = $row['count'];
              break;
          case 'GDT':
              $GDT = $row['count'];
              break;
      }
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
   
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
      <meta name="description" content="#">
      <meta name="keywords" content="flat ui, Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
      <meta name="author" content="#">
      
      <!-- Google font-->
      <link href="../../../../css.css?family=Mada:300,400,500,600,700" rel="stylesheet">
      <!-- Required Fremwork -->
      <link rel="stylesheet" type="text/css" href="bower_components/bootstrap/css/bootstrap.min.css">
      <!-- themify icon -->
      <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
      <!-- ico font -->
      <link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
      <!-- flag icon framework css -->
      <link rel="stylesheet" type="text/css" href="assets/pages/flag-icon/flag-icon.min.css">
      <!--SVG Icons Animated-->
      <link rel="stylesheet" type="text/css" href="assets/icon/SVG-animated/svg-weather.css">
      <!-- Menu-Search css -->
      <link rel="stylesheet" type="text/css" href="assets/pages/menu-search/css/component.css">
      <!-- Horizontal-Timeline css -->
      <link rel="stylesheet" type="text/css" href="assets/pages/dashboard/horizontal-timeline/css/style.css">
      <!-- amchart css -->
      <link rel="stylesheet" type="text/css" href="assets/pages/dashboard/amchart/css/amchart.css">
      <!-- Calender css -->
      <link rel="stylesheet" type="text/css" href="assets/pages/widget/calender/pignose.calendar.min.css">
      <!-- flag icon framework css -->
      <link rel="stylesheet" type="text/css" href="assets/pages/flag-icon/flag-icon.min.css">
      <!-- Style.css -->
      <link rel="stylesheet" type="text/css" href="assets/css/style.css">
      <!--color css-->


      <link rel="stylesheet" type="text/css" href="assets/css/linearicons.css">
      <link rel="stylesheet" type="text/css" href="assets/css/simple-line-icons.css">
      <link rel="stylesheet" type="text/css" href="assets/css/ionicons.css">
      <link rel="stylesheet" type="text/css" href="assets/css/jquery.mCustomScrollbar.css">
<style>
body {
    overflow: hidden;
}

.logo-link {
    display: inline-block;
    max-width: 180px;
}
.logo-img {
    width: 100%;
    height: auto;
    max-height: 50px;
    object-fit: contain;
}
@media (max-width: 768px) {
    .logo-link {
        max-width: 150px;
    }
}
</style>
  </head>
  <body>
    <div style="width: 100%; margin-top: 15%; margin-left: 15%;"class="page-body">
                                        <div class="row">
                                            <div class="col-md-6 col-xl-6">
                                                <div class="card client-blocks dark-primary-border">
                                                    <div class="card-block">
                                                        <h5>Courses</h5>
                                                        <ul>
                                                            <li>
                                                                <i class="icofont icofont-document-folder"></i>
                                                            </li>
                                                            <li class="text-right">
                                                            <?php
                                                                $stmt = $db->prepare("SELECT COUNT(*) as total FROM courses");
                                                                $stmt->execute();
                                                                $result = $stmt->get_result();
                                                                $row = $result->fetch_assoc();
                                                                echo $row['total'];
                                                                $stmt->close();
                                                                ?>
                                                            </li>
                                                            <li class="text-right"> 
                                                            <p style="font-size: 15px; paddings: 10px;"><span style="padding-right: 10px;">MCAD : <?php echo $MCAD; ?></span><span style="border-left: 1.5px solid #000; padding-left: 10px;"> SLM: <?php echo $SLM; ?></span>   </p>
                                                            <p style="font-size: 15px; paddings: 10px;"><span style="padding-right: 10px;">PLM: <?php echo $PLM; ?></span><span style="border-left: 1.5px solid #000; padding-left: 10px;">GDT: <?php echo $GDT; ?>  </span>   </p>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Documents card end -->
                                            <!-- New clients card start -->
                                            <div class="col-md-6 col-xl-6">
                                                <div class="card client-blocks warning-border">
                                                    <div class="card-block">
                                                        <h5>Users</h5>
                                                        <ul>
                                                            <li>
                                                                <i class="icofont icofont-ui-user-group text-warning"></i>
                                                            </li>
                                                            <li class="text-right text-warning">
                                                                <?php
                                                                $stmt = $db->prepare("SELECT COUNT(*) as total FROM user_info");
                                                                $stmt->execute();
                                                                $result = $stmt->get_result();
                                                                $row = $result->fetch_assoc();
                                                                echo $row['total'];
                                                                $stmt->close();
                                                                ?>
                                                            </li>
                                                            <li class="text-right"> 
                                                            <p style="font-size: 15px; paddings: 10px;"><span style="padding-right: 10px;">Super Users: <?php echo $super_users_count; ?></span><span style="border-left: 1.5px solid #000; padding-left: 10px;"> Admins: <?php echo $admins_count; ?></span>   </p>
                                                            <p style="font-size: 15px; paddings: 10px;"><span style="padding-right: 10px;">Trainers: <?php echo $trainers_count; ?></span><span style="border-left: 1.5px solid #000; padding-left: 10px;">Trainees: <?php echo $trainees_count; ?>  </span>   </p>
                                                            
                                                           
                                                           
                                        </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                           
                                           

</body>

</html>
