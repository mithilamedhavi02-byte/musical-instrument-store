<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin'){
   header('location:login.php');
   exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Customer Orders | Admin</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<?php include 'admin_header.php'; ?>

<div class="main-content">
   <div class="container-fluid">
      <h2 class="fw-bold mb-4 text-dark">Sales & Orders</h2>

      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
         <div class="table-responsive">
            <table class="table table-hover align-middle">
               <thead class="table-light">
                  <tr>
                     <th class="py-3">Order ID</th>
                     <th class="py-3">Customer ID</th>
                     <th class="py-3">Created At</th> <th class="py-3">Price</th> <th class="py-3">Status</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     // Database eken orders table eka select kireema
                     $select_orders = mysqli_query($conn, "SELECT * FROM `orders` ORDER BY id DESC") or die('query failed');
                     
                     if(mysqli_num_rows($select_orders) > 0){
                        while($row = mysqli_fetch_assoc($select_orders)){
                  ?>
                  <tr>
                     <td class="fw-bold text-warning">#ORD-<?php echo $row['id']; ?></td>
                     <td class="small text-secondary">User #<?php echo $row['user_id']; ?></td>
                     
                     <td><?php echo isset($row['created_at']) ? $row['created_at'] : 'N/A'; ?></td>
                     <td class="fw-bold text-dark">£<?php echo isset($row['price']) ? $row['price'] : '0.00'; ?></td>
                     
                     <td><span class="badge bg-warning text-dark px-3 py-2 rounded-pill">In Processing</span></td>
                  </tr>
                  <?php 
                        }
                     } else {
                        echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No orders have been placed yet.</td></tr>";
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