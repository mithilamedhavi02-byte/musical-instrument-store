<?php
// header.php
function getHeader($title = 'Melody Masters') {
    $is_logged_in = isset($_SESSION['user_id']);
    $user_name = $is_logged_in ? $_SESSION['name'] : '';
    $user_role = $is_logged_in ? $_SESSION['role'] : '';
    
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($title) . ' | Melody Masters</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root {
                --primary: #2c3e50;
                --secondary: #3498db;
                --accent: #e67e22;
                --success: #27ae60;
                --danger: #e74c3c;
                --light: #ecf0f1;
                --dark: #2c3e50;
                --gray: #95a5a6;
            }
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #333;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
            }
            
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
            }
            
            /* Navigation */
            .navbar {
                background: linear-gradient(to right, var(--primary), #1a2530);
                color: white;
                padding: 1rem 0;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                position: sticky;
                top: 0;
                z-index: 1000;
            }
            
            .navbar .container {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .logo {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 1.8rem;
                font-weight: 700;
                color: white;
                text-decoration: none;
            }
            
            .logo i {
                color: var(--accent);
            }
            
            .nav-links {
                display: flex;
                gap: 1.5rem;
                align-items: center;
            }
            
            .nav-links a {
                color: white;
                text-decoration: none;
                font-weight: 500;
                padding: 0.5rem 1rem;
                border-radius: 4px;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 5px;
            }
            
            .nav-links a:hover {
                background: rgba(255,255,255,0.1);
                transform: translateY(-2px);
            }
            
            .user-info {
                display: flex;
                align-items: center;
                gap: 10px;
                background: rgba(255,255,255,0.1);
                padding: 5px 15px;
                border-radius: 20px;
            }
            
            .user-info span {
                font-weight: 500;
            }
            
            .btn-logout {
                background: var(--danger);
                color: white;
                border: none;
                padding: 5px 15px;
                border-radius: 20px;
                cursor: pointer;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            
            .btn-logout:hover {
                background: #c0392b;
            }
            
            /* Main Content */
            .main-content {
                padding: 2rem 0;
                min-height: calc(100vh - 200px);
            }
            
            .card {
                background: white;
                border-radius: 10px;
                padding: 1.5rem;
                box-shadow: 0 5px 15px rgba(0,0,0,0.08);
                margin-bottom: 1.5rem;
                transition: transform 0.3s ease;
            }
            
            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            }
            
            /* Footer */
            .footer {
                background: var(--primary);
                color: white;
                padding: 2rem 0;
                margin-top: 3rem;
            }
            
            .footer-content {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 2rem;
            }
            
            .footer-section h3 {
                margin-bottom: 1rem;
                color: var(--accent);
            }
            
            .social-links {
                display: flex;
                gap: 1rem;
                margin-top: 1rem;
            }
            
            .social-links a {
                color: white;
                font-size: 1.2rem;
                transition: color 0.3s ease;
            }
            
            .social-links a:hover {
                color: var(--accent);
            }
            
            .copyright {
                text-align: center;
                margin-top: 2rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(255,255,255,0.1);
                color: var(--gray);
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .navbar .container {
                    flex-direction: column;
                    gap: 1rem;
                }
                
                .nav-links {
                    flex-wrap: wrap;
                    justify-content: center;
                }
            }
        </style>
    </head>
    <body>
        <nav class="navbar">
            <div class="container">
                <a href="index.php" class="logo">
                    <i class="fas fa-guitar"></i>
                    MELODY MASTERS
                </a>
                
                <div class="nav-links">';
                
    // Navigation links based on user status
    echo '<a href="index.php"><i class="fas fa-home"></i> Shop</a>';
    
    if ($is_logged_in) {
        echo '<a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>';
        echo '<a href="orders.php"><i class="fas fa-receipt"></i> Orders</a>';
        
        if ($user_role === 'admin') {
            echo '<a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Product</a>';
            echo '<a href="admin.php"><i class="fas fa-cog"></i> Admin</a>';
        }
        
        echo '<div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span>' . htmlspecialchars($user_name) . '</span>
                <a href="logout.php" class="btn-logout">Logout</a>
              </div>';
    } else {
        echo '<a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
              <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>';
    }
    
    echo '</div>
            </div>
        </nav>
        
        <main class="main-content">
            <div class="container">';
}
?>