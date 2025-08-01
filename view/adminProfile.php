
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Profile</title>
  <link rel="stylesheet" href="css/adminProfile.css">
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
    <h2 class="profile-form-title">Admin Profile Info</h2>
    <div class="profile-form-top">
      <div class="profile-img-box">
        <?php
        if (!empty($admin['profilePicture'])) {
          if (file_exists($admin['profilePicture'])) {
            $src = $admin['profilePicture'];
          } else {
            $imgData = base64_encode($admin['profilePicture']);
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
          <input type="file" name="profilePicture" accept="image/*" style="display:none;"
            onchange="imagePreview(event)">
        </label>
        <?php if (!empty($admin['profilePicture'])): ?>
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
          <input type="text" name="fullname" value="<?php echo htmlspecialchars($admin['fullName'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Contact Number</label>
          <input type="text" name="contactNumber"
            value="<?php echo htmlspecialchars($admin['contactNumber'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter new password">
        </div>
        <div class="form-group">
          <label>Confirm password</label>
          <input type="password" name="confirmPassword">
        </div>
      </div>
    </div>
    <button type="submit" class="save-btn">Confirm and Save</button>
  </form>
</body>

</html>