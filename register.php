<?php 
include 'config.php'; 

if (isset($_POST['register'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Admin ge email eken register wenna dhenne naha
    if ($email === 'admin@melody.com') {
        echo "<div class='alert alert-danger text-center'>This email is reserved for Admin!</div>";
    } else {
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure hashing
        $sql = "INSERT INTO users (username, email, password, role) VALUES ('$user', '$email', '$pass', 'customer')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<div class='alert alert-success text-center'>Registration successful! <a href='login.php' class='btn btn-sm btn-primary'>Login Now</a></div>";
        } else {
            echo "<div class='alert alert-danger text-center'>Error: Email might already exist!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Melody Masters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container border rounded bg-white p-4 shadow-sm" style="max-width: 450px;">
        <h2 class="text-center mb-4">Customer Register</h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Create Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" name="register" class="btn btn-success w-100 py-2">Create Account</button>
        </form>
        <hr>
        <p class="text-center mt-3 mb-0">Already have an account? <a href="login.php" class="text-decoration-none">Login Here</a></p>
    </div>
</body>
</html>