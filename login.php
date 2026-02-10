<?php
// login.php - SIMPLE VERSION
session_start();
require_once "includes/db.php";

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

// Process login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        $sql = "SELECT * FROM users WHERE email = '" . escape($email) . "'";
        $result = query($sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = fetch_one($result);
            
            // Check password
            if (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                
                // Initialize sessions
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                if (!isset($_SESSION['wishlist'])) {
                    $_SESSION['wishlist'] = [];
                }
                
                // Redirect based on role
                if ($user['role'] == 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - The Music Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .brand-logo i {
            font-size: 48px;
            color: #d4af37;
            margin-bottom: 10px;
        }
        .form-control:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }
        .btn-login {
            background-color: #121212;
            color: white;
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background-color: #d4af37;
            color: #000;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .register-link a {
            color: #d4af37;
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="brand-logo">
                <i class="fas fa-guitar"></i>
                <h3 class="fw-bold">The Music Shop</h3>
                <p class="text-muted">Sign in to your account</p>
            </div>
            
            <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Enter your email" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                    </div>
                </div>
                
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> Sign In
                    </button>
                </div>
                
                <div class="text-center mb-3">
                    <a href="forgot_password.php" class="text-decoration-none small">
                        Forgot your password?
                    </a>
                </div>
            </form>
            
            <div class="register-link">
                Don't have an account? 
                <a href="register.php">Register here</a>
            </div>
            
            <div class="text-center mt-4">
                <a href="index.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Back to Home
                </a>
            </div>
        </div>
        
        <!-- Test Credentials -->
        <div class="card mt-4">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-key me-2"></i> Test Credentials</h6>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Admin:</small><br>
                        <small>admin@musicshop.lk</small><br>
                        <small>admin123</small>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Customer:</small><br>
                        <small>john@example.com</small><br>
                        <small>customer123</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide password
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const passwordGroup = passwordInput.parentElement;
            
            // Add show password button
            const showPasswordBtn = document.createElement('button');
            showPasswordBtn.type = 'button';
            showPasswordBtn.className = 'btn btn-outline-secondary';
            showPasswordBtn.innerHTML = '<i class="fas fa-eye"></i>';
            showPasswordBtn.style.borderLeft = 'none';
            
            showPasswordBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
            
            passwordGroup.appendChild(showPasswordBtn);
        });
    </script>
</body>
</html>