<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Settings</title>
  <link rel="stylesheet" href="css/userSettings.css">
  <script type="text/javascript">
    function imagePreview(event) {
      if (event.target.files.length > 0) {
        var src = URL.createObjectURL(event.target.files[0]);
        var preview = document.getElementById("previewImage");
        preview.src = src;
        preview.style.display = "block";
      }
    }
  </script>
</head>
<body>
    <form class="profile-form" method="post" enctype="multipart/form-data" action="?command=updateProfileSettings">
      <h2 class="profile-form-title">Profile Info</h2>
      <div class="profile-form-top">
        <div class="profile-img-box">
          <?php
          if (!empty($users['profilePicture'])) {
              // Check if it's a file path or binary data
              if (file_exists($users['profilePicture'])) {
                  $src = $users['profilePicture'];
              } else {
                  // Assume it's binary data
                  $imgData = base64_encode($users['profilePicture']);
                  $src = 'data:image/jpeg;base64,' . $imgData;
              }
          } else {
              $src = 'images/default-profile.jpg';
          }
          ?>
          <img src="<?php echo $src; ?>" alt="Profile Picture" class="profile-img" id="previewImage">
        </div>
        <div class="profile-img-actions">
          <label class="change-picture-btn">
            Change picture
            <input type="file" name="profilePicture" accept="image/*" style="display:none;" onchange="imagePreview(event)">
          </label>
          <?php if (!empty($users['profilePicture'])): ?>
          <label class="delete-picture-btn">
            <input type="checkbox" name="removeProfilePicture" value="1" style="display:none;" id="removeProfilePicture">
            <span onclick="document.getElementById('removeProfilePicture').checked = true;">Remove picture</span>
          </label>
          <?php endif; ?>
        </div>
      </div>
      <div class="profile-form-fields">
        <div class="form-row">
          <div class="form-group">
            <label>Fullname</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($users['fullName'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label>Zone</label>
            <input type="text" name="zone" value="<?php echo htmlspecialchars($users['zone'] ?? ''); ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($users['email'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label>Contact Number</label>
            <input type="text" name="contactNumber" value="<?php echo htmlspecialchars($users['contactNumber'] ?? ''); ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter new password">
          </div>
          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($users['username'] ?? ''); ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Confirm password</label>
            <input type="password" name="confirmPassword">
          </div>
          <div class="form-group">
            <!-- Empty for alignment -->
          </div>
        </div>
      </div>
      <button type="submit" class="save-btn">confirm and save</button>
    </form>
</body>
</html>