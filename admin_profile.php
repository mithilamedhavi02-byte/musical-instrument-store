<?php
include 'config.php';

// Notice eka ain karanna me widiyata check karala danna
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin'){
   header('location:login.php');
   exit();
}

$admin_id = $_SESSION['user_id'];

// Admin details database eken ganeema
$select_profile = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$admin_id'") or die('query failed');
$fetch_profile = mysqli_fetch_assoc($select_profile);

// Profile eka update kireeme logic eka
if(isset($_POST['update_profile'])){
   $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
   $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);

   mysqli_query($conn, "UPDATE `users` SET username = '$update_name', email = '$update_email' WHERE id = '$admin_id'") or die('query failed');
   
   // Session eka update kireema
   $_SESSION['user_name'] = $update_name;
   header('location:admin_profile.php?success=1');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Profile | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <style>
      .profile-card { border: none; border-radius: 20px; overflow: hidden; }
      .profile-header { background: #1a1d20; color: #ffc107; padding: 40px 20px; text-align: center; }
      .profile-img-container { width: 120px; height: 120px; background: #ffc107; border-radius: 50%; margin: 0 auto -60px; display: flex; align-items: center; justify-content: center; font-size: 50px; border: 5px solid #f8f9fa; position: relative; z-index: 1; }
      .profile-body { padding-top: 70px; background: #fff; }
   </style>
</head>
<body class="bg-light">

<?php include 'admin_header.php'; ?>

<div class="main-content">
   <div class="container">
      <div class="row justify-content-center">
         <div class="col-md-6">
            
            <?php if(isset($_GET['success'])): ?>
               <div class="alert alert-success border-0 shadow-sm mb-4">Profile updated successfully!</div>
            <?php endif; ?>

            <div class="card profile-card shadow">
               <div class="profile-header">
                  <h4 class="fw-bold mb-0">ADMIN PROFILE</h4>
                  <p class="small opacity-75">Melody Masters Control Center</p>
               </div>
               
               <div class="profile-img-container shadow-sm">
                  <i class="fas fa-user-tie text-dark"></i>
               </div>

               <div class="card-body profile-body px-5 pb-5">
                  <form action="" method="post">
                     <div class="mb-3">
                        <label class="form-label fw-bold small">Full Name</label>
                        <div class="input-group">
                           <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                           <input type="text" name="update_name" class="form-control bg-light border-0" value="<?php echo $fetch_profile['username']; ?>" required>
                        </div>
                     </div>

                     <div class="mb-4">
                        <label class="form-label fw-bold small">Email Address</label>
                        <div class="input-group">
                           <span class="input-group-text bg-light border-0"><i class="fas fa-envelope text-muted"></i></span>
                           <input type="email" name="update_email" class="form-control bg-light border-0" value="<?php echo $fetch_profile['email']; ?>" required>
                        </div>
                     </div>

                     <div class="row g-2">
                        <div class="col-6">
                           <button type="submit" name="update_profile" class="btn btn-warning w-100 fw-bold py-2 shadow-sm">Save Changes</button>
                        </div>
                        <div class="col-6">
                           <a href="admin_dashboard.php" class="btn btn-outline-dark w-100 fw-bold py-2">Back</a>
                        </div>
                     </div>
                  </form>
               </div>
            </div>

            <div class="text-center mt-4">
               <p class="text-muted small">Account Role: <span class="badge bg-danger rounded-pill px-3"><?php echo strtoupper($fetch_profile['role']); ?></span></p>
            </div>

         </div>
      </div>
   </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>