<?php 
include_once 'config.php'; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$u_id = $_SESSION['user_id'] ?? 0;

$cart_q = mysqli_query($conn, "SELECT id FROM `cart` WHERE user_id = '$u_id'");
$cart_count = ($cart_q) ? mysqli_num_rows($cart_q) : 0;

$wish_q = mysqli_query($conn, "SELECT id FROM `wishlist` WHERE user_id = '$u_id'");
$wish_count = ($wish_q) ? mysqli_num_rows($wish_q) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melody Masters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar { background: #1a1a1a !important; border-bottom: 2px solid #ffc107; padding: 10px 0; }
        .nav-link { color: white !important; font-weight: 500; transition: 0.3s; }
        .nav-link:hover { color: #ffc107 !important; }
        
        .nav-icon { font-size: 1.2rem; position: relative; color: white !important; transition: 0.3s; }
        .nav-icon:hover { color: #ffc107 !important; }
        
        .badge-count { 
            position: absolute; top: -8px; right: -10px; font-size: 0.6rem; 
            background: #ffc107; color: black; padding: 2px 5px; border: 1.5px solid #1a1a1a;
        }
        
        .user-initial-circle {
            width: 35px; height: 35px; background-color: #ffc107; color: #000; 
            display: flex; align-items: center; justify-content: center; 
            border-radius: 50%; font-weight: bold; border: 2px solid white;
        }

        /* Mobile specific adjustments */
        @media (max-width: 991px) {
            .navbar-brand { font-size: 1.2rem !important; }
            .nav-icons-wrapper { gap: 15px !important; margin-right: 10px; }
            .navbar-toggler { padding: 4px 8px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-lg">
    <div class="container">
        <a class="navbar-brand fw-bold text-warning" href="index.php">🎵 MELODY MASTERS</a>
        
        <div class="d-flex align-items-center gap-3 ms-auto me-3 d-lg-none nav-icons-wrapper">
            <a href="wishlist.php" class="nav-icon">
                <i class="fa-regular fa-heart"></i>
                <span class="badge rounded-pill badge-count"><?php echo $wish_count; ?></span>
            </a>
            <a href="cart.php" class="nav-icon">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="badge rounded-pill badge-count"><?php echo $cart_count; ?></span>
            </a>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#melodyNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="melodyNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link px-3 text-center" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-center" href="products.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-center" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-center" href="contact.php">Contact</a></li>
            </ul>

            <div class="d-none d-lg-flex align-items-center gap-4">
                <a href="wishlist.php" class="nav-icon">
                    <i class="fa-regular fa-heart"></i>
                    <span class="badge rounded-pill badge-count"><?php echo $wish_count; ?></span>
                </a>
                <a href="cart.php" class="nav-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="badge rounded-pill badge-count"><?php echo $cart_count; ?></span>
                </a>
            </div>

            <div class="dropdown d-flex justify-content-center align-items-center mt-3 mt-lg-0 ms-lg-4">
                <?php if(isset($_SESSION['user_id'])): 
                    $u_email = $_SESSION['user_email'] ?? ($_SESSION['user_name'] ?? 'User');
                    $initial = strtoupper(substr($u_email, 0, 1));
                ?>
                    <div class="d-flex align-items-center" data-bs-toggle="dropdown" style="cursor: pointer;">
                        <div class="user-initial-circle"><?php echo $initial; ?></div>
                        <i class="fa-solid fa-caret-down ms-1 text-warning"></i>
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                        <li class="px-3 py-2 bg-light">
                            <span class="d-block fw-bold text-dark small text-truncate" style="max-width: 150px;">
                                <?php echo $u_email; ?>
                            </span>
                        </li>
                        <li><a class="dropdown-item mt-2" href="account.php"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
                    </ul>
                <?php else: ?>
                    <a href="login.php" class="nav-icon text-decoration-none">
                        <i class="fa-regular fa-user"></i> <span class="d-lg-none ms-2" style="font-size: 1rem;">Login</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>