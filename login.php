<?php
include 'config.php';

// Notice eka fix kireema: Session eka active da balala start kireema
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_POST['submit'])){
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

   // Database select
   $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

   if(mysqli_num_rows($select_users) > 0){
      $row = mysqli_fetch_assoc($select_users);

      // Parana session data okkoma clear karala aluthinma hadamu
      session_unset();
      
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['user_name'] = $row['username'];
      $_SESSION['user_role'] = trim($row['role']);

      // REDIRECTION
      if($_SESSION['user_role'] == 'admin'){
          header('location:admin_dashboard.php');
      } else {
          header('location:products.php');
      }
      exit();

   } else {
      $message[] = 'Incorrect email or password!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <style>
      body { background-color: #f8f9fa; }
      .login-card { width: 400px; border-radius: 15px; border: none; }
      .btn-warning { background-color: #ffc107; border: none; }
      .btn-warning:hover { background-color: #eab000; }
   </style>
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
   <div class="card shadow login-card p-4">
      <h3 class="text-center fw-bold mb-4 text-warning">Login Now</h3>
      
      <?php if(isset($message)){ foreach($message as $msg){ echo '<div class="alert alert-danger py-2 small">'.$msg.'</div>'; } } ?>

      <form action="" method="post" autocomplete="off">
         
         <div class="mb-3">
            <label class="form-label small fw-bold">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required autocomplete="off">
         </div>

         <div class="mb-4">
            <label class="form-label small fw-bold">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required autocomplete="new-password">
         </div>

         <button type="submit" name="submit" class="btn btn-warning w-100 fw-bold shadow-sm py-2">Login Now</button>
         
         <p class="text-center mt-3 small text-muted">Don't have an account? <a href="register.php" class="text-warning text-decoration-none fw-bold">Register here</a></p>
      </form>
   </div>
</div>

</body>
</html>