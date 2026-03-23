<?php
// admin/orders.php
session_start();
require_once "../includes/db.php";

// Check admin authentication
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Update order status
if(isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    $update_query = "UPDATE orders SET status = ? WHERE order_id = ?";
    db_query($update_query, [$status, $order_id]);
    header("Location: orders.php");
    exit();
}

// Get all orders
$query = "SELECT o.*, u.name as customer_name, u.email 
          FROM orders o 
          JOIN users u ON o.user_id = u.user_id 
          ORDER BY o.created_at DESC";
$stmt = db_query($query);
$orders = db_fetch_all($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100">
                <div class="p-3">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-music me-2"></i>Admin Panel
                    </h4>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a href="dashboard.php" class="nav-link text-white">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="manage_products.php" class="nav-link text-white">
                                <i class="fas fa-box me-2"></i> Products
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="admin_add_product.php" class="nav-link text-white">
                                <i class="fas fa-plus me-2"></i> Add Product
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="orders.php" class="nav-link text-white bg-primary rounded">
                                <i class="fas fa-shopping-cart me-2"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="users.php" class="nav-link text-white">
                                <i class="fas fa-users me-2"></i> Users
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a href="../index.php" class="nav-link text-white">
                                <i class="fas fa-store me-2"></i> View Shop
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../logout.php" class="nav-link text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <h2 class="fw-bold mb-4">Manage Orders</h2>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orders as $order): ?>
                                    <tr>
                                        <td>#<?= $order['order_id'] ?></td>
                                        <td>
                                            <div><?= $order['customer_name'] ?></div>
                                            <small class="text-muted"><?= $order['email'] ?></small>
                                        </td>
                                        <td>Rs. <?= number_format($order['total_amount'], 2) ?></td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                                <select name="status" class="form-select form-select-sm" 
                                                        onchange="this.form.submit()">
                                                    <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                                    <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                                    <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                                    <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                                <button type="submit" name="update_status" class="d-none">Update</button>
                                            </form>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                        <td>
                                            <a href="order_details.php?id=<?= $order['order_id'] ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>