<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <link rel="stylesheet" href="css/login.css" />
</head>

<body>
  <div class="login-container">
    <div class="logo-side">
      <img src="images/basura logo.png" alt="Logo" class="logo" />
      <h1>B.A.S.U.R.A. Rewards</h1>
      <p id="loginType">Official Admin Login</p>
    </div>
    <div class="form-side">
      <form class="login-card" method="POST" action="?command=login">
        <h2>Login</h2>
        <!-- Example error message -->
        <!-- <div class="alert">Invalid credentials</div> -->
        <label for="loginRole">Login As</label>
        <select id="loginRole" name="loginRole" required onchange="toggleLoginType()">
          <option value="user">User</option>
          <option value="admin">Admin</option>
          <option value="super admin">Super Admin</option>
        </select>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password</label>
        <div class="password-container">
          <input type="password" id="password" name="password" required>
          <button type="button" class="password-toggle" onclick="togglePassword()" id="passwordToggle">Show</button>
        </div>
        <button type="submit" class="btn-primary">Login</button>
        <p class="register-link">
          Don't have an account yet?<br>
        </p>
        <a href="?command=register" class="btn-secondary">Register</a>
      </form>
    </div>
  </div>

  <script>
    function toggleLoginType() {
      const role = document.getElementById('loginRole').value;
      const loginType = document.getElementById('loginType');
      loginType.textContent = role === 'user' ? "Official User Login" : "Official Admin Login";
    }

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

    document.addEventListener('DOMContentLoaded', toggleLoginType);
  </script>
</body>

</html>