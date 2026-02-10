<?php
// login.php - COMPLETE SECURE LOGIN SYSTEM
session_start();
require_once "includes/db.php";

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $redirect_url = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : 'index.php';
    header("Location: $redirect_url");
    exit();
}

$error = '';
$success = '';
$email = '';

// Process login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Get database connection
        $conn = get_db_connection();
        
        // Check for brute force attempts
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $attempt_time = time() - 900; // 15 minutes ago
        
        // Check recent failed attempts
        $check_sql = "SELECT COUNT(*) as attempts FROM login_attempts 
                     WHERE ip_address = ? AND attempt_time > ?";
        $attempts = fetch_one_prepared($check_sql, [$ip_address, $attempt_time], 'si');
        
        if ($attempts && $attempts['attempts'] >= 5) {
            $error = "Too many failed login attempts. Please try again in 15 minutes.";
        } else {
            // Use prepared statement to prevent SQL injection
            $sql = "SELECT * FROM users WHERE email = ? AND active = 1 LIMIT 1";
            $user = fetch_one_prepared($sql, [$email], 's');
            
            if ($user) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Check if password needs rehashing
                    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                        $new_hash = password_hash($password, PASSWORD_DEFAULT);
                        $update_sql = "UPDATE users SET password = ? WHERE user_id = ?";
                        execute_prepared_no_fetch($update_sql, [$new_hash, $user['user_id']], 'si');
                    }
                    
                    // Login successful - set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_role'] = $user['user_role'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['login_time'] = time();
                    
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    
                    // Update last login time and IP
                    $update_sql = "UPDATE users SET last_login = NOW(), last_ip = ? WHERE user_id = ?";
                    execute_prepared_no_fetch($update_sql, [$ip_address, $user['user_id']], 'si');
                    
                    // Clear any failed login attempts for this IP
                    $clear_sql = "DELETE FROM login_attempts WHERE ip_address = ?";
                    execute_prepared_no_fetch($clear_sql, [$ip_address], 's');
                    
                    // Remember me - Set secure cookie for 30 days
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $expiry = time() + (86400 * 30); // 30 days
                        
                        // Set secure cookie
                        setcookie('remember_token', $token, [
                            'expires' => $expiry,
                            'path' => '/',
                            'secure' => isset($_SERVER['HTTPS']),
                            'httponly' => true,
                            'samesite' => 'Strict'
                        ]);
                        
                        // Store hashed token in database
                        $hashed_token = hash('sha256', $token);
                        $token_sql = "UPDATE users SET remember_token = ?, token_expiry = FROM_UNIXTIME(?) WHERE user_id = ?";
                        execute_prepared_no_fetch($token_sql, [$hashed_token, $expiry, $user['user_id']], 'sii');
                    }
                    
                    // Initialize sessions
                    if (!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = [];
                    }
                    if (!isset($_SESSION['wishlist'])) {
                        $_SESSION['wishlist'] = [];
                    }
                    
                    // Set a session timeout (2 hours)
                    $_SESSION['session_expiry'] = time() + (2 * 60 * 60);
                    
                    // Check for redirect URL
                    $redirect_url = isset($_POST['redirect']) ? htmlspecialchars($_POST['redirect']) : 'index.php';
                    
                    // Redirect based on role
                    if ($user['user_role'] == 'admin') {
                        header("Location: admin/dashboard.php");
                    } else {
                        header("Location: $redirect_url");
                    }
                    exit();
                } else {
                    // Invalid password - log failed attempt
                    $log_sql = "INSERT INTO login_attempts (email, ip_address, attempt_time) VALUES (?, ?, ?)";
                    execute_prepared_no_fetch($log_sql, [$email, $ip_address, time()], 'ssi');
                    
                    $remaining = 5 - ($attempts['attempts'] ?? 0) - 1;
                    if ($remaining > 0) {
                        $error = "Invalid email or password. {$remaining} attempts remaining.";
                    } else {
                        $error = "Too many failed attempts. Please try again later.";
                    }
                }
            } else {
                $error = "Invalid email or password or account is inactive";
            }
        }
    }
}

// Check for redirect parameter
$redirect = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : '';

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #7209b7;
            --accent: #f72585;
            --gold: #ffd700;
            --dark-gold: #daa520;
            --black: #121212;
            --dark: #1a1a1a;
            --darker: #0f0f0f;
            --white: #ffffff;
            --light: #f8f9fa;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --success: #4cc9f0;
            --error: #f72585;
            --warning: #ff9e00;
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --gradient-4: linear-gradient(135deg, #7209b7 0%, #3a0ca3 100%);
            --gradient-gold: linear-gradient(135deg, #ffd700 0%, #daa520 100%);
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 5px 20px rgba(0, 0, 0, 0.2);
            --shadow-lg: 0 15px 40px rgba(0, 0, 0, 0.3);
            --shadow-gold: 0 0 20px rgba(255, 215, 0, 0.3);
            --radius-sm: 10px;
            --radius-md: 15px;
            --radius-lg: 20px;
            --radius-xl: 30px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gradient-1);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(118, 75, 162, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(247, 37, 133, 0.05) 0%, transparent 50%);
            z-index: -2;
        }

        /* Floating animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes float-delayed {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            33% { transform: translateY(-15px) translateX(10px); }
            66% { transform: translateY(10px) translateX(-10px); }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: var(--shadow-gold); }
            50% { box-shadow: 0 0 30px rgba(255, 215, 0, 0.5); }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Floating elements */
        .floating-element {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            z-index: -1;
        }

        .floating-element:nth-child(1) {
            width: 300px;
            height: 300px;
            top: 10%;
            left: 5%;
            animation: float 20s ease-in-out infinite;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.2), transparent 70%);
        }

        .floating-element:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: 20%;
            right: 10%;
            animation: float-delayed 25s ease-in-out infinite;
            background: radial-gradient(circle, rgba(118, 75, 162, 0.2), transparent 70%);
        }

        .floating-element:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 50%;
            left: 15%;
            animation: float 30s ease-in-out infinite reverse;
            background: radial-gradient(circle, rgba(247, 37, 133, 0.15), transparent 70%);
        }

        .login-wrapper {
            width: 100%;
            max-width: 1200px;
            display: flex;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            animation: slideInUp 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
        }

        .login-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            z-index: -1;
        }

        /* Left Panel - Welcome Section */
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, rgba(26, 26, 26, 0.95), rgba(15, 15, 15, 0.95)), 
                        url('https://images.unsplash.com/photo-1525201548942-d8732f6617a0?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-blend-mode: overlay;
            color: var(--white);
            padding: 70px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255, 215, 0, 0.1), transparent);
            z-index: 0;
        }

        .login-left-content {
            position: relative;
            z-index: 1;
            animation: fadeIn 1s ease-out 0.3s both;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 40px;
        }

        .brand-logo-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient-gold);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-gold);
            animation: pulse-glow 2s infinite;
        }

        .brand-logo-icon i {
            font-size: 28px;
            color: var(--black);
        }

        .brand-logo-text h1 {
            font-size: 28px;
            font-weight: 700;
            background: var(--gradient-gold);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin: 0;
        }

        .brand-logo-text p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            margin: 5px 0 0;
        }

        .welcome-heading {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 25px;
            background: linear-gradient(135deg, var(--white), var(--gold));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .welcome-heading span {
            display: block;
            font-size: 2.8rem;
            color: var(--gold);
        }

        .welcome-text {
            font-size: 1.2rem;
            line-height: 1.8;
            margin-bottom: 40px;
            color: rgba(255, 255, 255, 0.9);
            max-width: 600px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 50px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            padding: 25px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.6s;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--gold);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
        }

        .feature-icon-wrapper {
            width: 70px;
            height: 70px;
            background: var(--gradient-gold);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: var(--shadow-gold);
        }

        .feature-icon-wrapper i {
            font-size: 28px;
            color: var(--black);
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 10px;
        }

        .feature-description {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
        }

        /* Right Panel - Login Form */
        .login-right {
            flex: 1;
            padding: 70px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
            position: relative;
        }

        .login-right::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--gradient-gold);
        }

        .login-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .login-icon {
            width: 100px;
            height: 100px;
            background: var(--gradient-3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 15px 35px rgba(67, 97, 238, 0.3);
            animation: float 6s ease-in-out infinite;
            position: relative;
        }

        .login-icon::after {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            border: 3px solid rgba(67, 97, 238, 0.1);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        .login-icon i {
            font-size: 42px;
            color: var(--white);
        }

        .login-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 10px;
            background: var(--gradient-3);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .login-subtitle {
            font-size: 1.1rem;
            color: var(--gray);
            max-width: 400px;
            margin: 0 auto;
        }

        /* Alerts */
        .alert-container {
            margin-bottom: 30px;
        }

        .alert {
            border-radius: var(--radius-lg);
            border: none;
            padding: 20px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideInUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(247, 37, 133, 0.1), rgba(247, 37, 133, 0.05));
            color: var(--error);
            border-left: 5px solid var(--error);
        }

        .alert-danger::before {
            background: var(--error);
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(76, 201, 240, 0.1), rgba(76, 201, 240, 0.05));
            color: var(--success);
            border-left: 5px solid var(--success);
        }

        .alert-success::before {
            background: var(--success);
        }

        .alert-icon {
            font-size: 24px;
        }

        .alert-content {
            flex: 1;
        }

        .alert-content strong {
            display: block;
            margin-bottom: 5px;
        }

        /* Form Styles */
        .form-container {
            animation: fadeIn 0.8s ease-out 0.2s both;
        }

        .form-group {
            margin-bottom: 30px;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 12px;
        }

        .form-label i {
            color: var(--primary);
            font-size: 18px;
        }

        .input-group {
            position: relative;
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 2px solid var(--light-gray);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: var(--light);
        }

        .input-group:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
        }

        .input-group.error {
            border-color: var(--error);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .input-group-icon {
            width: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gradient-3);
            color: var(--white);
            font-size: 20px;
        }

        .form-control {
            border: none;
            padding: 18px 20px;
            font-size: 1rem;
            background: transparent;
            height: 60px;
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: none;
            background: transparent;
        }

        .password-toggle-btn {
            background: transparent;
            border: none;
            color: var(--gray);
            width: 60px;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle-btn:hover {
            color: var(--primary);
        }

        .form-text {
            margin-top: 8px;
            font-size: 0.875rem;
            color: var(--gray);
            padding-left: 10px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            border: 2px solid var(--light-gray);
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .form-check-input:checked {
            background: var(--gradient-3);
            border-color: var(--primary);
        }

        .form-check-label {
            font-size: 0.95rem;
            color: var(--black);
            cursor: pointer;
            font-weight: 500;
            user-select: none;
        }

        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            background: rgba(67, 97, 238, 0.1);
        }

        .forgot-link:hover {
            color: var(--primary-dark);
            background: rgba(67, 97, 238, 0.2);
            text-decoration: none;
            transform: translateY(-2px);
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 20px;
            font-size: 1.1rem;
            font-weight: 600;
            background: var(--gradient-3);
            color: var(--white);
            border: none;
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .btn-submit:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(67, 97, 238, 0.4);
        }

        .btn-submit:active {
            transform: translateY(-2px);
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit i {
            font-size: 20px;
            transition: transform 0.3s;
        }

        .btn-submit:hover i {
            transform: translateX(5px);
        }

        .btn-submit.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-submit.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Divider */
        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--light-gray), transparent);
        }

        .divider-text {
            display: inline-block;
            padding: 0 20px;
            background: var(--white);
            color: var(--gray);
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }

        /* Social Login */
        .social-login {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .btn-social {
            padding: 16px;
            border-radius: var(--radius-lg);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .btn-social i {
            font-size: 20px;
        }

        .btn-google {
            background: var(--white);
            color: #DB4437;
            border-color: #DB4437;
        }

        .btn-google:hover {
            background: #DB4437;
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(219, 68, 55, 0.2);
        }

        .btn-facebook {
            background: var(--white);
            color: #4267B2;
            border-color: #4267B2;
        }

        .btn-facebook:hover {
            background: #4267B2;
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(66, 103, 178, 0.2);
        }

        /* Register Link */
        .register-section {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--light-gray);
        }

        .register-text {
            color: var(--gray);
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .btn-register {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 40px;
            background: var(--gradient-4);
            color: var(--white);
            text-decoration: none;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 25px rgba(114, 9, 183, 0.2);
        }

        .btn-register:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(114, 9, 183, 0.3);
            color: var(--white);
            text-decoration: none;
        }

        .btn-register i {
            font-size: 18px;
            transition: transform 0.3s;
        }

        .btn-register:hover i {
            transform: translateX(5px);
        }

        /* Back Link */
        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--gray);
            text-decoration: none;
            font-size: 0.95rem;
            padding: 12px 24px;
            border-radius: var(--radius-md);
            transition: all 0.3s;
            background: rgba(0, 0, 0, 0.05);
        }

        .btn-back:hover {
            color: var(--primary);
            background: rgba(67, 97, 238, 0.1);
            text-decoration: none;
            transform: translateX(-5px);
        }

        .btn-back i {
            transition: transform 0.3s;
        }

        .btn-back:hover i {
            transform: translateX(-5px);
        }

        /* Test Credentials */
        .test-credentials {
            margin-top: 50px;
            background: linear-gradient(135deg, var(--light), #f0f2f5);
            border-radius: var(--radius-lg);
            padding: 30px;
            border-left: 5px solid var(--gold);
            box-shadow: var(--shadow-sm);
            animation: fadeIn 1s ease-out 0.6s both;
        }

        .test-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .test-header i {
            font-size: 24px;
            color: var(--gold);
        }

        .test-header h5 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--black);
            margin: 0;
        }

        .test-description {
            color: var(--gray);
            font-size: 0.95rem;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .credentials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .credential-card {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 25px;
            box-shadow: var(--shadow-sm);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .credential-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-md);
            border-color: var(--gold);
        }

        .credential-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--gradient-gold);
        }

        .credential-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(255, 215, 0, 0.1);
            color: var(--dark-gold);
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .credential-badge i {
            font-size: 16px;
        }

        .credential-details {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .credential-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .credential-label {
            font-weight: 600;
            color: var(--black);
            min-width: 80px;
        }

        .credential-value {
            color: var(--gray);
            font-family: 'Courier New', monospace;
            background: var(--light);
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            flex: 1;
            font-size: 0.95rem;
            position: relative;
            cursor: pointer;
            transition: all 0.3s;
        }

        .credential-value:hover {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }

        .credential-value::after {
            content: 'Click to copy';
            position: absolute;
            top: 50%;
            right: -80px;
            transform: translateY(-50%);
            font-size: 0.8rem;
            color: var(--primary);
            opacity: 0;
            transition: opacity 0.3s;
            white-space: nowrap;
        }

        .credential-value:hover::after {
            opacity: 1;
        }

        .copy-success {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            color: var(--success);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .credential-value.copied .copy-success {
            opacity: 1;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .login-wrapper {
                max-width: 900px;
            }
            
            .login-left,
            .login-right {
                padding: 50px 40px;
            }
        }

        @media (max-width: 992px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 600px;
            }
            
            .login-left {
                padding: 40px 30px;
            }
            
            .login-right {
                padding: 40px 30px;
            }
            
            .welcome-heading {
                font-size: 2.5rem;
            }
            
            .welcome-heading span {
                font-size: 2.2rem;
            }
            
            .login-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .login-wrapper {
                border-radius: var(--radius-lg);
            }
            
            .login-left,
            .login-right {
                padding: 30px 20px;
            }
            
            .brand-logo {
                margin-bottom: 30px;
            }
            
            .welcome-heading {
                font-size: 2rem;
            }
            
            .welcome-heading span {
                font-size: 1.8rem;
            }
            
            .login-title {
                font-size: 1.8rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .social-login {
                grid-template-columns: 1fr;
            }
            
            .credentials-grid {
                grid-template-columns: 1fr;
            }
            
            .form-options {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .floating-element {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .login-icon {
                width: 80px;
                height: 80px;
            }
            
            .login-icon i {
                font-size: 32px;
            }
            
            .btn-register,
            .btn-submit {
                padding: 16px;
            }
            
            .credential-value::after {
                display: none;
            }
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(10px);
        }

        .loading-overlay.active {
            display: flex;
            animation: fadeIn 0.3s ease-out;
        }

        .spinner {
            width: 80px;
            height: 80px;
            border: 5px solid rgba(255, 255, 255, 0.1);
            border-top: 5px solid var(--gold);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* Password Strength Meter */
        .password-strength {
            height: 6px;
            background: var(--light-gray);
            border-radius: 3px;
            margin-top: 10px;
            overflow: hidden;
            position: relative;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            background: var(--error);
            transition: all 0.3s ease;
            position: relative;
        }

        .strength-bar.weak {
            width: 33%;
            background: var(--error);
        }

        .strength-bar.medium {
            width: 66%;
            background: var(--warning);
        }

        .strength-bar.strong {
            width: 100%;
            background: var(--success);
        }

        .strength-text {
            position: absolute;
            right: 0;
            top: -20px;
            font-size: 0.8rem;
            color: var(--gray);
        }
    </style>
</head>
<body>
    <!-- Floating Background Elements -->
    <div class="floating-element"></div>
    <div class="floating-element"></div>
    <div class="floating-element"></div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
    
    <!-- Main Login Container -->
    <div class="login-wrapper">
        <!-- Left Panel - Welcome & Features -->
        <div class="login-left">
            <div class="login-left-content">
                <div class="brand-logo">
                    <div class="brand-logo-icon">
                        <i class="fas fa-guitar"></i>
                    </div>
                    <div class="brand-logo-text">
                        <h1>The Music Shop</h1>
                        <p>Premium Instruments & Equipment</p>
                    </div>
                </div>
                
                <h1 class="welcome-heading">
                    Welcome Back
                    <span>Musician!</span>
                </h1>
                
                <p class="welcome-text">
                    Discover our exclusive collection of premium musical instruments. 
                    From classic guitars to modern synthesizers, find the perfect 
                    instrument to express your musical talent.
                </p>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h3 class="feature-title">Free Shipping</h3>
                        <p class="feature-description">
                            Enjoy free shipping on all orders over Rs. 10,000. 
                            Fast delivery to your doorstep.
                        </p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Secure Payment</h3>
                        <p class="feature-description">
                            100% secure payment processing with SSL encryption. 
                            Your data is always protected.
                        </p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-headphones-alt"></i>
                        </div>
                        <h3 class="feature-title">24/7 Support</h3>
                        <p class="feature-description">
                            Our expert customer support team is available 
                            round the clock to assist you.
                        </p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h3 class="feature-title">Premium Quality</h3>
                        <p class="feature-description">
                            All our instruments are carefully selected for 
                            their exceptional quality and sound.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Login Form -->
        <div class="login-right">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <h1 class="login-title">Sign In</h1>
                <p class="login-subtitle">
                    Enter your credentials to access your account and explore our collection
                </p>
            </div>
            
            <!-- Alerts -->
            <div class="alert-container">
                <?php if($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle alert-icon"></i>
                    <div class="alert-content">
                        <strong>Login Failed</strong>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle alert-icon"></i>
                    <div class="alert-content">
                        <strong>Success</strong>
                        <p><?php echo htmlspecialchars($success); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Login Form -->
            <div class="form-container">
                <form method="POST" action="" id="loginForm" novalidate>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <!-- Redirect URL -->
                    <?php if(!empty($redirect)): ?>
                    <input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
                    <?php endif; ?>
                    
                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <div class="input-group" id="emailGroup">
                            <span class="input-group-icon">
                                <i class="fas fa-at"></i>
                            </span>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   placeholder="you@example.com"
                                   value="<?php echo htmlspecialchars($email); ?>"
                                   required
                                   autocomplete="email">
                        </div>
                        <div class="form-text">Enter your registered email address</div>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-group" id="passwordGroup">
                            <span class="input-group-icon">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="••••••••"
                                   required
                                   autocomplete="current-password"
                                   minlength="6">
                            <button type="button" class="password-toggle-btn" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <!-- Password Strength Meter -->
                        <div class="password-strength" id="passwordStrength">
                            <div class="strength-bar" id="strengthBar"></div>
                            <div class="strength-text" id="strengthText"></div>
                        </div>
                    </div>
                    
                    <!-- Options -->
                    <div class="form-options">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me for 30 days
                            </label>
                        </div>
                        <a href="forgot_password.php" class="forgot-link">
                            <i class="fas fa-question-circle"></i> Forgot Password?
                        </a>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign In</span>
                    </button>
                </form>
                
                <!-- Divider -->
                <div class="divider">
                    <span class="divider-text">Or continue with</span>
                </div>
                
                <!-- Social Login -->
                <div class="social-login">
                    <a href="#" class="btn-social btn-google">
                        <i class="fab fa-google"></i>
                        <span>Google</span>
                    </a>
                    <a href="#" class="btn-social btn-facebook">
                        <i class="fab fa-facebook-f"></i>
                        <span>Facebook</span>
                    </a>
                </div>
                
                <!-- Register Section -->
                <div class="register-section">
                    <p class="register-text">Don't have an account yet?</p>
                    <a href="register.php" class="btn-register">
                        <i class="fas fa-user-plus"></i>
                        <span>Create New Account</span>
                    </a>
                </div>
                
                <!-- Back Link -->
                <div class="back-link">
                    <a href="index.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Homepage</span>
                    </a>
                </div>
                
                <!-- Test Credentials -->
                <div class="test-credentials">
                    <div class="test-header">
                        <i class="fas fa-key"></i>
                        <h5>Test Credentials</h5>
                    </div>
                    <p class="test-description">
                        Use these credentials to test the login system. Click on any credential to copy it.
                    </p>
                    <div class="credentials-grid">
                        <div class="credential-card">
                            <div class="credential-badge">
                                <i class="fas fa-user-shield"></i>
                                <span>Admin Account</span>
                            </div>
                            <div class="credential-details">
                                <div class="credential-row">
                                    <span class="credential-label">Email:</span>
                                    <span class="credential-value" data-value="admin@musicshop.lk">
                                        admin@musicshop.lk
                                        <i class="fas fa-check copy-success"></i>
                                    </span>
                                </div>
                                <div class="credential-row">
                                    <span class="credential-label">Password:</span>
                                    <span class="credential-value" data-value="admin123">
                                        admin123
                                        <i class="fas fa-check copy-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="credential-card">
                            <div class="credential-badge">
                                <i class="fas fa-user"></i>
                                <span>Customer Account</span>
                            </div>
                            <div class="credential-details">
                                <div class="credential-row">
                                    <span class="credential-label">Email:</span>
                                    <span class="credential-value" data-value="john@example.com">
                                        john@example.com
                                        <i class="fas fa-check copy-success"></i>
                                    </span>
                                </div>
                                <div class="credential-row">
                                    <span class="credential-label">Password:</span>
                                    <span class="credential-value" data-value="customer123">
                                        customer123
                                        <i class="fas fa-check copy-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const loginForm = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const submitBtn = document.getElementById('submitBtn');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const emailGroup = document.getElementById('emailGroup');
            const passwordGroup = document.getElementById('passwordGroup');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            const passwordStrength = document.getElementById('passwordStrength');
            
            // Auto-focus email field
            setTimeout(() => {
                emailInput.focus();
            }, 500);
            
            // Toggle password visibility
            togglePasswordBtn.addEventListener('click', function() {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                this.innerHTML = type === 'password' ? 
                    '<i class="fas fa-eye"></i>' : 
                    '<i class="fas fa-eye-slash"></i>';
            });
            
            // Real-time email validation
            emailInput.addEventListener('blur', validateEmail);
            emailInput.addEventListener('input', validateEmail);
            
            // Password strength indicator
            passwordInput.addEventListener('input', updatePasswordStrength);
            
            // Form submission
            loginForm.addEventListener('submit', handleSubmit);
            
            // Credential copy functionality
            setupCredentialCopy();
            
            // Functions
            function validateEmail() {
                const email = emailInput.value.trim();
                const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                
                if (!email) {
                    resetInput(emailGroup);
                    return false;
                }
                
                if (isValid) {
                    markValid(emailGroup);
                } else {
                    markInvalid(emailGroup, 'Please enter a valid email address');
                }
                
                return isValid;
            }
            
            function updatePasswordStrength() {
                const password = passwordInput.value;
                let strength = 0;
                
                if (!password) {
                    passwordStrength.style.display = 'none';
                    return;
                }
                
                passwordStrength.style.display = 'block';
                
                // Length check
                if (password.length >= 8) strength += 1;
                if (password.length >= 12) strength += 1;
                
                // Complexity checks
                if (/[A-Z]/.test(password)) strength += 1;
                if (/[a-z]/.test(password)) strength += 1;
                if (/[0-9]/.test(password)) strength += 1;
                if (/[^A-Za-z0-9]/.test(password)) strength += 1;
                
                // Update UI
                strengthBar.className = 'strength-bar';
                
                if (strength <= 2) {
                    strengthBar.classList.add('weak');
                    strengthText.textContent = 'Weak';
                    strengthText.style.color = 'var(--error)';
                } else if (strength <= 4) {
                    strengthBar.classList.add('medium');
                    strengthText.textContent = 'Medium';
                    strengthText.style.color = 'var(--warning)';
                } else {
                    strengthBar.classList.add('strong');
                    strengthText.textContent = 'Strong';
                    strengthText.style.color = 'var(--success)';
                }
                
                // Validate password
                validatePassword();
            }
            
            function validatePassword() {
                const password = passwordInput.value;
                
                if (!password) {
                    resetInput(passwordGroup);
                    return false;
                }
                
                if (password.length >= 6) {
                    markValid(passwordGroup);
                    return true;
                } else {
                    markInvalid(passwordGroup, 'Password must be at least 6 characters');
                    return false;
                }
            }
            
            function handleSubmit(e) {
                e.preventDefault();
                
                // Validate form
                const isEmailValid = validateEmail();
                const isPasswordValid = validatePassword();
                
                if (isEmailValid && isPasswordValid) {
                    // Show loading
                    showLoading(true);
                    submitBtn.classList.add('loading');
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Signing In...</span>';
                    
                    // Submit form after delay
                    setTimeout(() => {
                        loginForm.submit();
                    }, 1500);
                } else {
                    // Scroll to first error
                    const firstError = document.querySelector('.input-group.error');
                    if (firstError) {
                        firstError.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                        
                        // Add shake animation
                        firstError.style.animation = 'none';
                        setTimeout(() => {
                            firstError.style.animation = 'shake 0.5s ease-in-out';
                        }, 10);
                    }
                }
            }
            
            function markValid(inputGroup) {
                inputGroup.classList.remove('error');
                inputGroup.style.borderColor = 'var(--success)';
            }
            
            function markInvalid(inputGroup, message) {
                inputGroup.classList.add('error');
                inputGroup.style.borderColor = 'var(--error)';
                
                // Show tooltip
                const tooltip = inputGroup.querySelector('.error-tooltip');
                if (tooltip) tooltip.remove();
                
                const errorTooltip = document.createElement('div');
                errorTooltip.className = 'error-tooltip';
                errorTooltip.style.cssText = `
                    position: absolute;
                    top: 100%;
                    left: 0;
                    right: 0;
                    background: var(--error);
                    color: white;
                    padding: 8px 12px;
                    border-radius: 5px;
                    font-size: 0.9rem;
                    margin-top: 5px;
                    z-index: 1000;
                `;
                errorTooltip.textContent = message;
                
                inputGroup.appendChild(errorTooltip);
            }
            
            function resetInput(inputGroup) {
                inputGroup.classList.remove('error');
                inputGroup.style.borderColor = 'var(--light-gray)';
                
                const tooltip = inputGroup.querySelector('.error-tooltip');
                if (tooltip) tooltip.remove();
            }
            
            function showLoading(show) {
                if (show) {
                    loadingOverlay.classList.add('active');
                } else {
                    loadingOverlay.classList.remove('active');
                }
            }
            
            function setupCredentialCopy() {
                const credentialValues = document.querySelectorAll('.credential-value');
                
                credentialValues.forEach(value => {
                    value.addEventListener('click', function() {
                        const text = this.getAttribute('data-value');
                        
                        // Copy to clipboard
                        navigator.clipboard.writeText(text).then(() => {
                            // Show success state
                            this.classList.add('copied');
                            
                            // Reset after 2 seconds
                            setTimeout(() => {
                                this.classList.remove('copied');
                            }, 2000);
                            
                            // Show notification
                            showNotification('Credential copied to clipboard!');
                        }).catch(err => {
                            console.error('Failed to copy: ', err);
                            showNotification('Failed to copy. Please try again.', 'error');
                        });
                    });
                });
            }
            
            function showNotification(message, type = 'success') {
                // Remove existing notification
                const existing = document.querySelector('.copy-notification');
                if (existing) existing.remove();
                
                // Create notification
                const notification = document.createElement('div');
                notification.className = `copy-notification ${type}`;
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${type === 'success' ? 'var(--success)' : 'var(--error)'};
                    color: white;
                    padding: 15px 25px;
                    border-radius: var(--radius-md);
                    box-shadow: var(--shadow-md);
                    z-index: 9999;
                    animation: slideInUp 0.3s ease-out;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                `;
                
                notification.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}"></i>
                    <span>${message}</span>
                `;
                
                document.body.appendChild(notification);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    notification.style.animation = 'slideInUp 0.3s ease-out reverse';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
            
            // Prevent form resubmission on page refresh
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
            
            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+Enter to submit form
                if (e.ctrlKey && e.key === 'Enter') {
                    submitBtn.click();
                }
                
                // Escape to clear form
                if (e.key === 'Escape') {
                    if (confirm('Clear all form fields?')) {
                        loginForm.reset();
                        emailInput.focus();
                        updatePasswordStrength();
                    }
                }
            });
            
            // Test credential auto-fill
            document.querySelectorAll('.credential-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    if (!e.target.classList.contains('credential-value')) {
                        const email = this.querySelector('.credential-value[data-value*="@"]').getAttribute('data-value');
                        const password = this.querySelector('.credential-value:not([data-value*="@"])').getAttribute('data-value');
                        
                        emailInput.value = email;
                        passwordInput.value = password;
                        
                        // Trigger validation
                        validateEmail();
                        updatePasswordStrength();
                        
                        // Focus password field
                        passwordInput.focus();
                        
                        // Highlight the card
                        this.style.transform = 'scale(1.02)';
                        this.style.boxShadow = '0 20px 40px rgba(255, 215, 0, 0.3)';
                        
                        setTimeout(() => {
                            this.style.transform = '';
                            this.style.boxShadow = '';
                        }, 1000);
                        
                        showNotification('Credentials auto-filled!');
                    }
                });
            });
        });
    </script>
</body>
</html>