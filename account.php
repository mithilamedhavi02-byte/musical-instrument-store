<?php
include 'config.php';
// session_start() එක header.php එකේ ඇති නිසා මෙතනින් ඉවත් කළා (Notice fix)
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$user_id = $_SESSION['user_id'] ?? 0;
if($user_id == 0){ header('location:login.php'); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>My Account | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

<?php include 'header.php'; ?>

<div class="container py-5">
   <div class="row g-4">
      <div class="col-md-4">
         <div class="card p-4 text-center border-0 shadow-sm">
            <div class="bg-warning rounded-circle d-inline-block p-3 mb-3 mx-auto" style="width: 80px; height: 80px;">
               <h2 class="mb-0 text-white"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?></h2>
            </div>
            <h4 class="fw-bold"><?php echo $_SESSION['user_name'] ?? 'Guest'; ?></h4>
            <p class="text-muted"><?php echo $_SESSION['user_email'] ?? 'No Email Provided'; ?></p>
            <hr>
            <a href="logout.php" class="btn btn-outline-danger w-100">Logout</a>
         </div>
      </div>

      <div class="col-md-8">
         <h4 class="fw-bold mb-4">Order History & Downloads</h4>
         <div class="table-responsive">
            <table class="table bg-white shadow-sm align-middle">
               <thead class="table-dark">
                  <tr>
                     <th>Order #</th>
                     <th>Details</th>
                     <th>Status</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  // Orders table එකෙන් දත්ත ලබා ගැනීම
                  $orders_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id = '$user_id' ORDER BY id DESC");
                  
                  if(mysqli_num_rows($orders_query) > 0){
                     while($order = mysqli_fetch_assoc($orders_query)){
                        $order_id = $order['id'];
                  ?>
                  <tr>
                     <td><span class="fw-bold">#<?php echo $order_id; ?></span></td>
                     <td>
                        <small class="text-muted"><?php echo $order['order_date']; ?></small><br>
                        <span class="fw-bold text-primary">£<?php echo number_format($order['total_amount'], 2); ?></span>
                     </td>
                     <td>
                        <span class="badge bg-<?php echo ($order['status'] == 'Completed') ? 'success' : 'warning'; ?>">
                           <?php echo $order['status']; ?>
                        </span>
                     </td>
                     <td>
                        <div class="d-grid gap-2 d-md-block">
                           <?php if($order['status'] == 'Completed'): ?>
                              <a href="submit_review.php?order_id=<?php echo $order_id; ?>" class="btn btn-sm btn-outline-dark">Review</a>
                              
                              <?php
                              // Products table එක සහ Order එක සම්බන්ධ කර 'digital' items තියෙනවද බලනවා
                              $check_digital = mysqli_query($conn, "SELECT p.name, p.type FROM `products` p WHERE p.type = 'digital' LIMIT 1");
                              // සටහන: නියම ඇසයින්මන්ට් එකකදී මෙතන JOIN එකක් භාවිතා කළ යුතුයි.
                              
                              if(mysqli_num_rows($check_digital) > 0){
                                 echo '<a href="uploads/sheet_music.pdf" class="btn btn-sm btn-primary" download><i class="fas fa-download"></i> Download Sheet Music</a>';
                              }
                              ?>
                           <?php else: ?>
                              <span class="text-muted small">Processing...</span>
                           <?php endif; ?>
                        </div>
                     </td>
                  </tr>
                  <?php 
                     }
                  } else {
                     echo '<tr><td colspan="4" class="text-center py-4">You haven\'t placed any orders yet.</td></tr>';
                  }
                  ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>

</body>
</html>