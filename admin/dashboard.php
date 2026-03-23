<?php
// admin/dashboard.php
session_start();
require_once "../includes/db.php";

// Check admin authentication
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get statistics
$stats = [];

// Total products
$products_query = "SELECT COUNT(*) as total FROM products";
$products_stmt = db_query($products_query);
$stats['products'] = db_fetch_one($products_stmt)['total'];

// Total orders
$orders_query = "SELECT COUNT(*) as total FROM orders";
$orders_stmt = db_query($orders_query);
$stats['orders'] = db_fetch_one($orders_stmt)['total'];

// Total users
$users_query = "SELECT COUNT(*) as total FROM users";
$users_stmt = db_query($users_query);
$stats['users'] = db_fetch_one($users_stmt)['total'];

// Total revenue
$revenue_query = "SELECT SUM(total_amount) as total FROM orders WHERE status = 'delivered'";
$revenue_stmt = db_query($revenue_query);
$stats['revenue'] = db_fetch_one($revenue_stmt)['total'] ?? 0;

// Recent orders
$recent_orders_query = "SELECT o.*, u.name as customer_name 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.user_id 
                       ORDER BY o.created_at DESC LIMIT 5";
$recent_orders_stmt = db_query($recent_orders_query);
$recent_orders = db_fetch_all($recent_orders_stmt);

// Recent products
$recent_products_query = "SELECT p.*, c.category_name 
                         FROM products p 
                         JOIN categories c ON p.category_id = c.category_id 
                         ORDER BY p.created_at DESC LIMIT 5";
$recent_products_stmt = db_query($recent_products_query);
$recent_products = db_fetch_all($recent_products_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
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
                            <a href="dashboard.php" class="nav-link text-white bg-primary rounded">
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
                            <a href="orders.php" class="nav-link text-white">
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
                <h2 class="fw-bold mb-4">Dashboard</h2>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Products</h6>
                                        <h3 class="fw-bold"><?= $stats['products'] ?></h3>
                                    </div>
                                    <i class="fas fa-box fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Orders</h6>
                                        <h3 class="fw-bold"><?= $stats['orders'] ?></h3>
                                    </div>
                                    <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Users</h6>
                                        <h3 class="fw-bold"><?= $stats['users'] ?></h3>
                                    </div>
                                    <i class="fas fa-users fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Revenue</h6>
                                        <h3 class="fw-bold">Rs. <?= number_format($stats['revenue'], 2) ?></h3>
                                    </div>
                                    <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders & Products -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3">Recent Orders</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($recent_orders as $order): ?>
                                            <tr>
                                                <td>#<?= $order['order_id'] ?></td>
                                                <td><?= $order['customer_name'] ?></td>
                                                <td>Rs. <?= number_format($order['total_amount'], 2) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= 
                                                        $order['status'] == 'delivered' ? 'success' : 
                                                        ($order['status'] == 'pending' ? 'warning' : 'info')
                                                    ?>">
                                                        <?= ucfirst($order['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="orders.php" class="btn btn-outline-primary btn-sm">View All Orders</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3">Recent Products</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($recent_products as $product): ?>
                                            <tr>
                                                <td><?= $product['name'] ?></td>
                                                <td><?= $product['category_name'] ?></td>
                                                <td>Rs. <?= number_format($product['price'], 2) ?></td>
                                                <td><?= $product['stock'] ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="manage_products.php" class="btn btn-outline-primary btn-sm">View All Products</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>