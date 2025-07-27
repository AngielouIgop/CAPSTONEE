<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register</title>
  <link rel="stylesheet" href="css/register.css" />
</head>

<body>
  <div class="register-container">
    <!-- Left Side (Logo and Name) -->
    <div class="logo-side">
      <img src="images/basura logo.png" alt="Logo" class="logo" />
      <h1>B.A.S.U.R.A. Rewards</h1>
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
        <label for="zone">Zone</label>
        <input type="text" name="zone" id="zone" required>
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
</body>

</html>