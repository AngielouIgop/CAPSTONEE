<?php include 'header.php'; ?>
<?php include 'contribute.php'; ?>

<script>
  var userID = '<?php echo $_SESSION['user']['userID']; ?>';
</script>
<script src="js/contributeModal.js"></script>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/dashboard.css" />
</head>
<body>
  <div class="content">
    <h2>Dashboard</h2>

    <div class="card-row">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Account Info</h5>
          <!-- <p class="card-text">View your personal details and account information.</p> -->
          <!-- <a href="?command=userProfile" class="btn-primary">Go to Profile</a> -->
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Settings</h5>
          <!-- <p class="card-text">Update your account settings, preferences, and more.</p> -->
          <!-- <a href="?command=settings" class="btn-primary">Go to Settings</a> -->
        </div>
      </div>
    </div>
  </div>
  
</body>
</html>

