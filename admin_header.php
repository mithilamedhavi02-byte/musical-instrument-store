<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

if(!isset($_SESSION['user_id'])){
   header('location:login.php');
   exit();
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
   <div class="container-fluid">
      <a class="navbar-brand fw-bold text-warning" href="admin_dashboard.php">
         <i class="fas fa-music me-2"></i>MELODY MASTERS <span class="badge bg-danger ms-2 small" style="font-size: 10px;">ADMIN PANEL</span>
      </a>
      
      <div class="ms-auto d-flex align-items-center">
         <div class="text-end me-3">
            <span class="d-block text-white small fw-bold"><?php echo $_SESSION['user_name']; ?></span>
            <span class="badge bg-success" style="font-size: 9px;">Online</span>
         </div>
         
        <div class="dropdown">
   <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm pointer" 
        style="width: 40px; height: 40px; cursor: pointer;" id="userMenu" data-bs-toggle="dropdown">
      <a href="admin_profile.php" class="text-dark"><i class="fas fa-user-tie"></i></a>
   </div>
   <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
      <li><a class="dropdown-item py-2" href="admin_profile.php"><i class="fas fa-user-circle me-2 text-primary"></i> My Profile</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item py-2" href="logout.php"><i class="fas fa-sign-out-alt me-2 text-danger"></i> Logout</a></li>
   </ul>
</div>
      </div>
   </div>
</nav>

<style>
   :root { --sidebar-width: 260px; --primary-color: #ffc107; --dark-bg: #1a1d20; }
   body { padding-top: 70px; background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
   .sidebar { height: 100vh; width: var(--sidebar-width); position: fixed; top: 0; left: 0; background: var(--dark-bg); padding-top: 85px; z-index: 1000; transition: all 0.3s; }
   .sidebar a { padding: 15px 25px; text-decoration: none; color: #94999f; display: flex; align-items: center; font-size: 15px; transition: 0.3s; border-left: 4px solid transparent; }
   .sidebar a i { width: 30px; font-size: 18px; }
   .sidebar a:hover, .sidebar a.active { background: #2d3238; color: var(--primary-color); border-left: 4px solid var(--primary-color); }
   .sidebar .section-title { padding: 20px 25px 10px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #5a6168; font-weight: bold; }
   .main-content { margin-left: var(--sidebar-width); padding: 40px; transition: all 0.3s; }
   @media (max-width: 992px) { .sidebar { left: -260px; } .main-content { margin-left: 0; } .sidebar.active { left: 0; } }
</style>

<div class="sidebar shadow">
   <div class="section-title">Main Menu</div>
   <a href="admin_dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php') ? 'active' : ''; ?>">
      <i class="fas fa-th-large"></i> Dashboard
   </a>
   
   <div class="section-title">Inventory & Sales</div>
   <a href="admin_products.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admin_products.php') ? 'active' : ''; ?>">
      <i class="fas fa-plus-circle"></i> Add Products
   </a>
   <a href="admin_orders.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admin_orders.php') ? 'active' : ''; ?>">
      <i class="fas fa-clipboard-list"></i> View Orders
   </a>
   
   <div class="section-title">Quick Links</div>
   <a href="products.php" target="_blank">
      <i class="fas fa-external-link-alt text-info"></i> Customer Shop
   </a>
</div>
