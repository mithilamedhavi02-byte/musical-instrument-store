<?php
// admin/manage_products.php
session_start();
require_once "../includes/db.php";

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $sql = "UPDATE products SET active = 0 WHERE product_id = $product_id";
    query($sql);
    header("Location: manage_products.php?deleted=true");
    exit();
}

// Get all products
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.active = 1 
        ORDER BY p.created_at DESC";
$result = query($sql);
$products = $result ? fetch_all($result) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .sidebar a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 5px;
            margin: 5px 0;
            transition: all 0.3s;
        }
        
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .content {
            padding: 20px;
        }
        
        .table-responsive {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .badge-in-stock {
            background: #28a745;
            color: white;
        }
        
        .badge-low-stock {
            background: #ffc107;
            color: black;
        }
        
        .badge-out-stock {
            background: #dc3545;
            color: white;
        }
        
        .btn-action {
            padding: 5px 10px;
            font-size: 0.85rem;
            margin: 2px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-4">
                    <h4 class="mb-4">Music Shop Admin</h4>
                    <div class="nav flex-column">
                        <a href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a href="admin_add_product.php">
                            <i class="fas fa-plus-circle me-2"></i> Add Product
                        </a>
                        <a href="manage_products.php" class="active">
                            <i class="fas fa-boxes me-2"></i> Manage Products
                        </a>
                        <a href="orders.php">
                            <i class="fas fa-shopping-cart me-2"></i> Orders
                        </a>
                        <a href="users.php">
                            <i class="fas fa-users me-2"></i> Users
                        </a>
                        <a href="../logout.php" class="mt-5">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>
                        <i class="fas fa-boxes me-2"></i> Manage Products
                    </h4>
                    <a href="admin_add_product.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Add New Product
                    </a>
                </div>
                
                <?php if(isset($_GET['deleted'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Product deleted successfully
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($products)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5>No Products Found</h5>
                                    <p class="text-muted">Add your first product</p>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($products as $product): 
                                $stock_class = 'badge-in-stock';
                                if ($product['stock_quantity'] == 0) {
                                    $stock_class = 'badge-out-stock';
                                } elseif ($product['stock_quantity'] <= 5) {
                                    $stock_class = 'badge-low-stock';
                                }
                                
                                $image_file = '../assets/images/products/product_' . $product['product_id'] . '.jpg';
                                if (!file_exists($image_file)) {
                                    $image_file = '../assets/images/products/default.jpg';
                                }
                            ?>
                            <tr>
                                <td>#<?= $product['product_id'] ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($image_file) ?>" 
                                         class="product-image" 
                                         alt="Product"
                                         onerror="this.src='../assets/images/products/default.jpg'">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($product['name']) ?></strong>
                                    <div class="text-muted small">
                                        <?= substr(htmlspecialchars($product['description']), 0, 50) ?>...
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($product['brand'] ?? 'N/A') ?></td>
                                <td>Rs. <?= number_format($product['price'], 2) ?></td>
                                <td>
                                    <span class="badge <?= $stock_class ?>">
                                        <?= $product['stock_quantity'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="edit_product.php?id=<?= $product['product_id'] ?>" 
                                           class="btn btn-sm btn-primary btn-action">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage_products.php?delete=<?= $product['product_id'] ?>" 
                                           class="btn btn-sm btn-danger btn-action"
                                           onclick="return confirm('Delete this product?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="../product_detail.php?id=<?= $product['product_id'] ?>" 
                                           target="_blank"
                                           class="btn btn-sm btn-info btn-action">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>