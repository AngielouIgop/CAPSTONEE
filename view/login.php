<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <link rel="stylesheet" href="css/login.css" />
  <link rel="stylesheet" href="css/header-footer.css">
</head>
<body>
  <div class="login-container">
    <div class="logo-side">
      <img src="images/basura logo.png" alt="Logo" class="logo" />
      <h1>Barangay Rewards</h1>
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
        </select>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <button type="submit" class="btn-primary">Login</button>
        <a href="?command=register" class="btn-secondary">Register</a>
      </form>
    </div>
  </div>
  
  <?php include_once("view/footer.php"); ?>
  
<script>
  function toggleLoginType() {
    const role = document.getElementById('loginRole').value;
    const loginType = document.getElementById('loginType');
    loginType.textContent = role === 'user' ? "Official User Login" : "Official Admin Login";
  }
  document.addEventListener('DOMContentLoaded', toggleLoginType);
</script>
</body>
</html>