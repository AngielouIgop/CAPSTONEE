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
        <div class="password-container">
          <input type="password" name="password" id="password" required>
          <button type="button" class="password-toggle" onclick="togglePassword()" id="passwordToggle">Show</button>
        </div>
        <label for="confirm">Confirm Password</label>
        <div class="password-container">
          <input type="password" name="confirm" id="confirm" required>
          <button type="button" class="password-toggle" onclick="togglePassword1()" id="confirmPasswordToggle">Show</button>
        </div>
        <button type="submit" class="btn-primary">Register</button>
        <p class="login-link">
          Already have an account? <a href="?command=login">Login here</a>
        </p>
      </form>
    </div>
  </div>
</body>

  <script>
function togglePassword() {
  const passwordInput = document.getElementById('password');
  const passwordToggle = document.getElementById('passwordToggle');

  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    passwordToggle.textContent = 'Hide';
    passwordToggle.title = 'Hide password';
  } else {
    passwordInput.type = 'password';
    passwordToggle.textContent = 'Show';
    passwordToggle.title = 'Show password';
  }
}

function togglePassword1() {
  const confirmInput = document.getElementById('confirm');
  const confirmToggle = document.getElementById('confirmPasswordToggle');

  if (confirmInput.type === 'password') {
    confirmInput.type = 'text';
    confirmToggle.textContent = 'Hide';
    confirmToggle.title = 'Hide confirm password';
  } else {
    confirmInput.type = 'password';
    confirmToggle.textContent = 'Show';
    confirmToggle.title = 'Show confirm password';
  }
}
</script>


</html>