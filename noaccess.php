<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Permission Denied</title>
  <style>
    body {
  margin: 0;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #202060; /* deep blue background */
  font-family: Arial, sans-serif;
}

.permission-card {
  display: flex;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.25);
  overflow: hidden;
  max-width: 800px;
  width: 90%;
}

.permission-left, .permission-right {
  flex: 1;
  padding: 40px;
  text-align: center;
}

.permission-left {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.permission-left .icon {
  font-size: 40px;
  color: crimson;
  margin-bottom: 15px;
}

.permission-left h2 {
  color: #222;
  margin: 10px 0;
}

.permission-left p {
  color: #555;
  font-size: 15px;
  line-height: 1.5;
}

.permission-right {
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #1e1f70, #2d2e90);
}

.permission-right img {
  max-width: 80%;
  height: auto;
}


    </style>
</head>
<body>
  <div class="permission-card">
    <div class="permission-left">
      <div class="icon">🔑</div>
      <h2>Permission Denied!</h2>
      <p>
        You do not have permissions to upload documents here.  
        Speak to your administrator to unlock this feature.
      </p>
    </div>
    <div class="permission-right">
      <img src="images/noaccess.png" alt="Access Denied Illustration">
    </div>
  </div>
</body>
</html>
