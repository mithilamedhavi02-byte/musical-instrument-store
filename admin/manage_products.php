<?php
// admin/manage_products.php
session_start();
require_once "../includes/db.php";

// Check admin authentication
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Delete product
if(isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $delete_query = "DELETE FROM products WHERE product_id = ?";
    db_query($delete_query, [$product_id]);
    header("Location: manage_products.php");
    exit();
}

// Get all products with categories
$query = "SELECT p.*, c.category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.category_id 
          ORDER BY p.created_at DESC";
$stmt = db_query($query);
$products = db_fetch_all($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Panel</title>
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
                            <a href="manage_products.php" class="nav-link text-white bg-primary rounded">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Manage Products</h2>
                    <a href="admin_add_product.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Add New Product
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($products as $product): ?>
                                    <tr>
                                        <td>#<?= $product['product_id'] ?></td>
                                        <td>
                                            <img src="<?= $product['image_url'] ?: 'https://via.placeholder.com/50' ?>" 
                                                 style="width: 50px; height: 50px; object-fit: cover;" 
                                                 class="rounded" alt="<?= $product['name'] ?>">
                                        </td>
                                        <td><?= $product['name'] ?></td>
                                        <td><?= $product['category_name'] ?></td>
                                        <td>Rs. <?= number_format($product['price'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $product['stock'] > 0 ? 'success' : 'danger' ?>">
                                                <?= $product['stock'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="../product_detail.php?id=<?= $product['product_id'] ?>" 
                                               class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit_product.php?id=<?= $product['product_id'] ?>" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="manage_products.php?delete=<?= $product['product_id'] ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Delete this product?')">
                                                <i class="fas fa-trash"></i>
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