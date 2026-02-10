<?php
// admin/admin_add_product.php
session_start();
require_once "../includes/db.php";

// Check admin authentication
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

// Get categories
$cat_query = "SELECT * FROM categories ORDER BY category_name";
$cat_stmt = db_query($cat_query);
$categories = db_fetch_all($cat_stmt);

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);
    $image_url = $_POST['image_url'];
    
    // Validation
    if(empty($name) || $price <= 0) {
        $error = "Please fill all required fields";
    } else {
        $insert_query = "INSERT INTO products (name, description, price, stock, category_id, image_url) 
                         VALUES (?, ?, ?, ?, ?, ?)";
        db_query($insert_query, [$name, $description, $price, $stock, $category_id, $image_url]);
        
        $success = "Product added successfully";
        unset($_POST);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Panel</title>
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
                            <a href="admin_add_product.php" class="nav-link text-white bg-primary rounded">
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
                            <a href="../logout.php" class="nav-link text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <h2 class="fw-bold mb-4">Add New Product</h2>
                
                <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Product Name *</label>
                                    <input type="text" name="name" class="form-control" required 
                                           value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Category *</label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['category_id'] ?>" 
                                            <?= isset($_POST['category_id']) && $_POST['category_id'] == $cat['category_id'] ? 'selected' : '' ?>>
                                            <?= $cat['category_name'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Price (Rs.) *</label>
                                    <input type="number" name="price" class="form-control" step="0.01" required 
                                           value="<?= isset($_POST['price']) ? $_POST['price'] : '' ?>">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Stock Quantity *</label>
                                    <input type="number" name="stock" class="form-control" required 
                                           value="<?= isset($_POST['stock']) ? $_POST['stock'] : '0' ?>">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Image URL</label>
                                    <input type="url" name="image_url" class="form-control" 
                                           value="<?= isset($_POST['image_url']) ? $_POST['image_url'] : '' ?>"
                                           placeholder="https://example.com/image.jpg">
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4"><?= isset($_POST['description']) ? $_POST['description'] : '' ?></textarea>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="fas fa-save me-2"></i> Save Product
                                    </button>
                                    <a href="manage_products.php" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>