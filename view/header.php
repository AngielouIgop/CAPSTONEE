<link rel="stylesheet" href="css/header-footer.css">
<link rel="stylesheet" href="css/uni-sidebar.css">
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="header-main">
  <div class="header-logo">
    <a href="?command=home">
      <img src="images/basura logo.png" alt="Basura Logo" class="logo-img" />
    </a>
    <p class="header-title"></p>
  </div>
  
  <?php if (!isset($_SESSION['user'])): ?>
  <nav class="header-nav">
    <ul>
      <li><a href="?command=home">Home</a></li>
      <li><a href="?command=home#about-us-section">About Us</a></li>
      <li><a href="?command=home#how-it-works">How it works</a></li>
      <li><a href="?command=login">Log in</a></li>
    </ul>
  </nav>
  <?php elseif (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'user'): ?>
  <nav class="header-nav">
    <ul>
      <li><a href="?command=contribute" class="start-contributing-btn">Start Contributing</a></li>
    </ul>
  </nav>
  <?php endif; ?>
</header>
<?php
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    // Admin sidebar
    ?>
    <div class="sidebar">
        <div>
            <h4 class="sidebar-title">Sidebar</h4>
            <div class="nav-links">
                <a href="?command=adminDashboard">Home</a>
                <a href="?command=manageUser">Manage Users</a>
                <a href="?command=rewardInventory">Reward Inventory</a>
                <a href="?command=adminReport">Reports</a>
                <a href="?command=adminProfile">My Profile</a>
                <a href="?command=logout">Logout</a>
            </div>
        </div>
        <!-- <div class="logout-link">
            <a href="?command=logout">Logout</a>
        </div> -->
    </div>
    <?php
} elseif (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'user') {
    // Regular user sidebar
    ?>
    <div class="sidebar">
        <div>
            <h4 class="sidebar-title">Sidebar</h4>
            <div class="nav-links">
                <a href="?command=dashboard">Home</a>
                <a href="?command=userProfile">Profile</a>
                <a href="?command=userSettings">Settings</a>
                <a href="?command=claim">Claim Rewards</a>
                <a href="?command=logout">Logout</a>
            </div>
        </div>
        <!-- <div class="logout-link">
            <a href="?command=logout">Logout</a>
        </div> -->
    </div>
    <?php
}
?>
