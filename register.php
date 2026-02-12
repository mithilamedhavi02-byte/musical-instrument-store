<?php
include 'config.php';

if(isset($_POST['submit'])){
   // Input data suddha kireema
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $user_type = 'user'; 

   // Validation 1: Name ekata numbers danna bari kireema
   if (!preg_match("/^[a-zA-Z\s]*$/", $name)) {
      $message[] = 'Invalid Name! Only letters and spaces are allowed.';
   } 
   // Validation 2: Email eka danatama thiyedai baleema
   else {
      $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die(mysqli_error($conn));

      if(mysqli_num_rows($select_users) > 0){
         $message[] = 'This email is already registered!';
      } else {
         // Data insert kireema (Error eka kelinma panna widihata)
         $insert_query = "INSERT INTO `users`(name, email, password, user_type) VALUES('$name', '$email', '$pass', '$user_type')";
         $insert = mysqli_query($conn, $insert_query);
         
         if($insert){
            echo "<script>alert('Registration Successful!'); window.location.href='login.php';</script>";
         } else {
            // "query failed" wenuwata aththama SQL error eka pennai
            die('Registration Failed: ' . mysqli_error($conn)); 
         }
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Register | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
   <div class="card shadow-lg border-0 p-4" style="width: 450px; border-radius: 20px;">
      <div class="text-center mb-4">
         <i class="fa-solid fa-music fa-3x text-warning mb-2"></i>
         <h3 class="fw-bold">Create Account</h3>
         <p class="text-muted small">Join Melody Masters Instrument Shop</p>
      </div>
      
      <?php
      if(isset($message)){
         foreach($message as $msg){
            echo '<div class="alert alert-danger py-2 small"><i class="fa-solid fa-triangle-exclamation me-2"></i>'.$msg.'</div>';
         }
      }
      ?>

      <form action="" method="post">
         <div class="mb-3">
            <label class="form-label small fw-bold">Full Name</label>
            <div class="input-group">
               <span class="input-group-text bg-white"><i class="fa-solid fa-user text-muted"></i></span>
               <input type="text" name="name" class="form-control" placeholder="John Doe" required>
            </div>
         </div>
         <div class="mb-3">
            <label class="form-label small fw-bold">Email Address</label>
            <div class="input-group">
               <span class="input-group-text bg-white"><i class="fa-solid fa-envelope text-muted"></i></span>
               <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
            </div>
         </div>
         <div class="mb-4">
            <label class="form-label small fw-bold">Password</label>
            <div class="input-group">
               <span class="input-group-text bg-white"><i class="fa-solid fa-lock text-muted"></i></span>
               <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
         </div>
         <button type="submit" name="submit" class="btn btn-warning w-100 fw-bold py-2 shadow-sm">REGISTER NOW</button>
         <p class="text-center mt-3 small">Already have an account? <a href="login.php" class="text-warning fw-bold text-decoration-none">Login now</a></p>
      </form>
   </div>
</div>

</body>
</html>