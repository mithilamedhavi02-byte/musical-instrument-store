<?php 
include_once 'config.php'; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// User ID eka gannawa
$u_id = $_SESSION['user_id'] ?? 0;

// Cart Count එක Database එකෙන් ගන්න (Fix)
$cart_q = mysqli_query($conn, "SELECT id FROM `cart` WHERE user_id = '$u_id'");
$cart_count = ($cart_q) ? mysqli_num_rows($cart_q) : 0;

// Wishlist Count එක Database එකෙන් ගන්න
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
        .navbar { background: #1a1a1a !important; border-bottom: 2px solid #ffc107; padding: 12px 0; }
        .nav-link { color: white !important; font-weight: 500; transition: 0.3s; }
        .nav-link:hover { color: #ffc107 !important; transform: translateY(-2px); }
        .nav-icon { font-size: 1.3rem; position: relative; color: white !important; transition: 0.3s; }
        .nav-icon:hover { color: #ffc107 !important; }
        .badge-count { 
            position: absolute; top: -10px; right: -12px; font-size: 0.65rem; 
            background: #ffc107; color: black; padding: 3px 6px; border: 2px solid #1a1a1a;
        }
        .user-initial-circle {
            width: 38px; height: 38px; background-color: #ffc107; color: #000; 
            display: flex; align-items: center; justify-content: center; 
            border-radius: 50%; font-weight: bold; border: 2px solid white; transition: 0.3s;
        }
        .user-initial-circle:hover { transform: scale(1.1); box-shadow: 0 0 10px rgba(255, 193, 7, 0.5); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-lg">
    <div class="container">
        <a class="navbar-brand fw-bold text-warning fs-3" href="index.php">🎵 MELODY MASTERS</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#melodyNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="melodyNavbar">
            <ul class="navbar-nav ms-auto me-4">
                <li class="nav-item"><a class="nav-link px-3" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="products.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="contact.php">Contact</a></li>
            </ul>

            <div class="d-flex align-items-center gap-4">
                <a href="wishlist.php" class="nav-icon">
                    <i class="fa-regular fa-heart"></i>
                    <span class="badge rounded-pill badge-count"><?php echo $wish_count; ?></span>
                </a>

                <a href="cart.php" class="nav-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="badge rounded-pill badge-count"><?php echo $cart_count; ?></span>
                </a>

                <div class="dropdown">
                    <a href="#" class="text-decoration-none" data-bs-toggle="dropdown">
                        <?php if(isset($_SESSION['user_id'])): 
                            $email = $_SESSION['user_email'] ?? 'U';
                            $initial = strtoupper(substr($email, 0, 1));
                        ?>
                            <div class="user-initial-circle"><?php echo $initial; ?></div>
                        <?php else: ?>
                            <div class="nav-icon"><i class="fa-regular fa-user"></i></div>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 animate slideIn">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li class="px-3 py-2 bg-light">
                                <span class="d-block fw-bold text-dark small text-truncate" style="max-width: 150px;">
                                    <?php echo $_SESSION['user_email']; ?>
                                </span>
                            </li>
                            <li><a class="dropdown-item mt-2" href="account.php"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="login.php">Login</a></li>
                            <li><a class="dropdown-item" href="register.php">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>