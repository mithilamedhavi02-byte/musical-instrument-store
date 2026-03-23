<?php
// includes/navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize sessions if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}
?>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: rgba(0,0,0,0.95); border-bottom: 2px solid #d4af37;">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="fas fa-compact-disc fa-spin me-2" style="color: #d4af37;"></i>
            <span class="fw-bold" style="letter-spacing: 1px;">Melody Masters Instrument Shop </span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold <?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>" href="products.php">
                        Store
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold <?= basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : '' ?>" href="services.php">
                        Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>" href="about.php">
                        About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>" href="contact.php">
                        Contact
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-4">
                <!-- Wishlist Link -->
                <a href="<?= isset($_SESSION['user_id']) ? 'wishlist.php' : 'login.php?redirect_from=wishlist' ?>" 
                   class="text-white position-relative text-decoration-none">
                    <i class="far fa-heart fa-lg"></i>
                    <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" 
                          style="font-size: 0.65rem; font-weight: bold;">
                        <?= count($_SESSION['wishlist']) ?>
                    </span>
                </a>
                
                <!-- Cart Link -->
                <a href="<?= isset($_SESSION['user_id']) ? 'cart.php' : 'login.php?redirect_from=cart' ?>" 
                   class="text-white position-relative text-decoration-none">
                    <i class="fas fa-shopping-basket fa-lg"></i>
                    <span class="badge rounded-pill bg-warning text-dark position-absolute top-0 start-100 translate-middle" 
                          style="font-size: 0.65rem; font-weight: bold;">
                        <?= count($_SESSION['cart']) ?>
                    </span>
                </a>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- Logged In User -->
                    <div class="dropdown">
                        <a href="#" class="text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-lg"></i> <?= $_SESSION['username'] ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-box me-2"></i> Orders</a></li>
                            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-primary" href="admin/dashboard.php"><i class="fas fa-cog me-2"></i> Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Login Button -->
                    <a href="login.php" class="btn btn-outline-warning btn-sm rounded-pill px-4 fw-bold">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>