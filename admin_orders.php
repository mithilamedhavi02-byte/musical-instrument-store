<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check
if(!isset($_SESSION['user_id']) || strtolower(trim($_SESSION['user_role'] ?? '')) !== 'admin'){
   header('location:login.php');
   exit();
}

// ==========================================
// ORDER STATUS UPDATE LOGIC
// ==========================================
if(isset($_POST['update_order'])){
   $order_id = $_POST['order_id'];
   $update_status = mysqli_real_escape_string($conn, $_POST['update_status']);
   
   // 'method_status' තීරුව පවතින බව තහවුරු කරගන්න
   $update_query = mysqli_query($conn, "UPDATE `orders` SET method_status = '$update_status' WHERE id = '$order_id'");
   
   if($update_query){
      $message[] = 'Order status has been updated!';
   } else {
      $error_msg = mysqli_error($conn); // Error එක බැලීමට
      die("Query Failed: $error_msg");
   }
}

// ==========================================
// ORDER DELETE LOGIC
// ==========================================
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_orders.php'); 
   exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Elite Orders | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
   
   <style>
      :root { --gold: #D4AF37; --dark-gold: #996515; --black: #0b0b0b; --sidebar-width: 260px; }
      body { background-color: #f8f8f8; font-family: 'Poppins', sans-serif; margin: 0; }
      .main-content { margin-left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); padding: 0 20px 80px 20px; margin-top: -60px; }
      .admin-hero { background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('https://images.unsplash.com/photo-1552422535-c45813c61732?auto=format&fit=crop&w=1600&q=80'); background-size: cover; padding: 100px 0 160px 0; color: white; text-align: center; clip-path: polygon(0 0, 100% 0, 100% 85%, 0% 100%); }
      .inventory-container { background: white; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; position: relative; z-index: 5; }
      .table thead { background: var(--black); color: var(--gold); }
      .status-select { padding: 5px 15px; border-radius: 50px; border: 1px solid var(--gold); font-size: 0.8rem; font-weight: 600; cursor: pointer; }
      .delete-btn { color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.3); padding: 6px 14px; border-radius: 10px; text-decoration: none; }
   </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<div class="admin-hero">
   <h1 style="font-family: 'Playfair Display', serif; color: var(--gold);">Sales & Royal Orders</h1>
   <p class="lead opacity-75">Review and manage your master collection sales</p>
</div>

<div class="main-content">
   <div class="container-fluid">
      <div class="inventory-container">
         <div class="table-responsive">
            <table class="table table-hover mb-0">
               <thead>
                  <tr>
                     <th class="ps-4">Reference</th>
                     <th>Customer</th>
                     <th>Total Valuation</th> 
                     <th>Update Status</th>
                     <th class="text-center">Operations</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $select_orders = mysqli_query($conn, "SELECT * FROM `orders` ORDER BY id DESC");
                     if(mysqli_num_rows($select_orders) > 0){
                        while($row = mysqli_fetch_assoc($select_orders)){
                  ?>
                  <tr>
                     <td class="ps-4 fw-bold text-warning">#ORD-<?php echo $row['id']; ?></td>
                     <td>
                        <div class="fw-bold">User #<?php echo $row['user_id']; ?></div>
                        <small class="text-muted"><?php echo $row['placed_on'] ?? 'Date N/A'; ?></small>
                     </td>
                     <td class="fw-bold">
                        Rs. <?php echo number_format($row['total_price'] ?? 0, 2); ?>
                     </td>
                     <td>
                        <form action="" method="post">
                           <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                           <select name="update_status" class="status-select" onchange="this.form.submit()">
                              <option value="" selected disabled><?php echo $row['method_status'] ?? 'Pending'; ?></option>
                              <option value="Processing">Processing</option>
                              <option value="Shipped">Shipped</option>
                              <option value="Delivered">Delivered</option>
                              <option value="Cancelled">Cancelled</option>
                           </select>
                           <input type="hidden" name="update_order" value="1">
                        </form>
                     </td>
                     <td class="text-center">
                        <a href="admin_orders.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Delete this order?');">
                           <i class="fas fa-trash"></i>
                        </a>
                     </td>
                  </tr>
                  <?php } } else { ?>
                     <tr><td colspan="5" class="text-center py-5 text-muted">No luxury orders recorded.</td></tr>
                  <?php } ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
</body>
</html>