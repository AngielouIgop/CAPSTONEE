<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register</title>
  <link rel="stylesheet" href="css/register.css" />
  <link rel="stylesheet" href="css/header-footer.css">
</head>
<body>
  <div class="register-container">
    <!-- Left Side (Logo and Name) -->
    <div class="logo-side">
      <img src="images/basura logo.png" alt="Logo" class="logo" />
      <h1>Barangay Rewards</h1>
      <p>Official User Registration</p>
    </div>
    <!-- Right Side (Registration Form) -->
    <div class="form-side">
      <form class="register-card" method="POST" action="?command=processRegister">
        <h3>Create an Account</h3>
        <?php if (!empty($error)): ?>
          <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>
        <label for="fullname">Full Name</label>
        <input type="text" name="fullname" id="fullname" required>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
        <label for="registerRole">Register as</label>
        <select name="registerRole" id="registerRole" required onchange="toggleFields()">
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>
        <div id="zoneField">
          <label for="zone">Zone</label>
          <input type="text" name="zone" id="zone" required>
        </div>
        <div id="positionField" style="display: none;">
          <label for="position">Position</label>
          <input type="text" name="position" id="position">
        </div>
        <label for="contactNumber">Contact Number</label>
        <input type="text" name="contactNumber" id="contactNumber" required>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
        <label for="confirm">Confirm Password</label>
        <input type="password" name="confirm" id="confirm" required>
        <button type="submit" class="btn-primary">Register</button>
        <p class="login-link">
          Already have an account? <a href="?command=login">Login here</a>
        </p>
      </form>
    </div>
  </div>
  
  <script>
    function toggleFields() {
      const role = document.getElementById('registerRole').value;
      const zoneField = document.getElementById('zoneField');
      const positionField = document.getElementById('positionField');
      const zoneInput = document.getElementById('zone');
      const positionInput = document.getElementById('position');
      
      if (role === 'admin') {
        zoneField.style.display = 'none';
        positionField.style.display = 'block';
        zoneInput.removeAttribute('required');
        positionInput.setAttribute('required', 'required');
      } else {
        zoneField.style.display = 'block';
        positionField.style.display = 'none';
        zoneInput.setAttribute('required', 'required');
        positionInput.removeAttribute('required');
      }
    }
  </script>
  
  <?php include_once("view/footer.php"); ?>
</body>
</html>