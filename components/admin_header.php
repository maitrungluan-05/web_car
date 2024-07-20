<?php
// Start the session
session_start();

// Ensure $admin_id is defined
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
} else {
    // Handle the case where the admin ID is not set
    die('Admin ID is not set.');
}

// Include your database connection file here
// For example: $conn = new PDO(...);

// Fetch the admin profile
$select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
$select_profile->execute([$admin_id]);

// Check if any row is fetched
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
?>

<?php
// Display messages if set
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<header class="header">
   <section class="flex">
      <a href="../admin/dashboard.php" class="logo">Admin<span>Panel</span></a>

      <nav class="navbar">
         <a href="../admin/dashboard.php">Home</a>
         <a href="../admin/products.php">Products</a>
         <a href="../admin/placed_orders.php">Orders</a>
         <a href="../admin/admin_accounts.php">Admins</a>
         <a href="../admin/users_accounts.php">Users</a>
         <a href="../admin/messages.php">Messages</a>
         <a href="../admin/add_category.php">Category</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php
         if ($fetch_profile) {
            echo '<p>' . htmlspecialchars($fetch_profile['name']) . '</p>';
         } else {
            echo '<p>Profile not found.</p>';
         }
         ?>
         <a href="../admin/update_profile.php" class="btn">Update profile</a>
         <div class="flex-btn">
            <a href="../admin/register_admin.php" class="option-btn">Register</a>
            <a href="../admin/admin_login.php" class="option-btn">Login</a>
         </div>
         <a href="../components/admin_logout.php" class="delete-btn" onclick="return confirm('Logout from the website?');">Logout</a> 
      </div>
   </section>
</header>
