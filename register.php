<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_POST['submit'])){
   $username = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $user_type = 'user'; 

   if (!preg_match("/^[a-zA-Z\s]*$/", $username)) {
      $message[] = 'Invalid Name! Only letters and spaces are allowed.';
   } else {
      $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die(mysqli_error($conn));

      if(mysqli_num_rows($select_users) > 0){
         $message[] = 'User already exists!';
      } else {
         $insert_query = "INSERT INTO `users`(username, email, password, user_type, role) VALUES('$username', '$email', '$pass', '$user_type', 'customer')";
         if(mysqli_query($conn, $insert_query)){
            echo "<script>alert('Registered successfully!'); window.location.href='login.php';</script>";
         } else {
            die('Query Failed: ' . mysqli_error($conn));
         }
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
   <div class="card shadow p-4" style="width: 400px; border-radius: 15px;">
      <h3 class="text-center fw-bold mb-4 text-warning">Register Now</h3>
      
      <?php if(isset($message)){ foreach($message as $msg){ echo '<div class="alert alert-danger py-2 small">'.$msg.'</div>'; } } ?>
      
      <form action="" method="post" autocomplete="off">
         <div class="mb-3">
            <label class="form-label small fw-bold">Full Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter name" required autocomplete="off">
         </div>
         <div class="mb-3">
            <label class="form-label small fw-bold">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter email" required autocomplete="off">
         </div>
         <div class="mb-4">
            <label class="form-label small fw-bold">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter password" required autocomplete="new-password">
         </div>
         <button type="submit" name="submit" class="btn btn-warning w-100 fw-bold">Register Now</button>
         <p class="text-center mt-3 small">Already have an account? <a href="login.php" class="text-warning">Login now</a></p>
      </form>
   </div>
</div>
</body>
</html>