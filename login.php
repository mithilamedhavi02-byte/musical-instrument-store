<?php 
include 'config.php'; 

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    // --- ADMIN UNIQUE LOGIN CHECK ---
    if ($email === 'admin@melody.com' && $pass === 'admin123') {
        $_SESSION['user_id'] = 0; 
        $_SESSION['role'] = 'admin';
        header('location:admin.php');
        exit();
    } 

    // --- CUSTOMER LOGIN CHECK ---
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'customer';
        header('location:index.php');
        exit();
    } else {
        echo "<div class='alert alert-danger text-center'>Invalid Email or Password!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Melody Masters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container border rounded bg-white p-4 shadow-sm" style="max-width: 400px;">
        <h2 class="text-center mb-4">Login</h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 py-2">Login</button>
        </form>
        <hr>
        <p class="text-center mt-3 mb-0">New to Melody Masters? <a href="register.php" class="text-decoration-none">Register Here</a></p>
    </div>
</body>
</html>