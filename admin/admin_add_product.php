<?php
// admin/admin_add_product.php
session_start();
require_once "../includes/db.php";

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = escape($_POST['name']);
    $description = escape($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $brand = escape($_POST['brand']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $spec_1 = escape($_POST['spec_1']);
    $spec_2 = escape($_POST['spec_2']);
    $spec_3 = escape($_POST['spec_3']);
    
    // Handle image upload
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = 'product_' . time() . '.' . $extension;
            $upload_path = '../assets/images/products/' . $image_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Image uploaded successfully
            }
        }
    }
    
    // Insert product
    $sql = "INSERT INTO products (name, description, price, category_id, brand, stock_quantity, spec_1, spec_2, spec_3, image_url, active) 
            VALUES ('$name', '$description', $price, $category_id, '$brand', $stock_quantity, '$spec_1', '$spec_2', '$spec_3', '$image_name', 1)";
    
    if (query($sql)) {
        $message = 'Product added successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error adding product. Please try again.';
        $message_type = 'danger';
    }
}

// Get categories for dropdown
$categories_result = query("SELECT * FROM categories WHERE active = 1 ORDER BY category_name");
$categories = $categories_result ? fetch_all($categories_result) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        
        .card {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .form-control, .form-select {
            border-radius: 5px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 30px;
            border-radius: 5px;
            font-weight: 500;
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
                        <a href="admin_add_product.php" class="active">
                            <i class="fas fa-plus-circle me-2"></i> Add Product
                        </a>
                        <a href="manage_products.php">
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
                <div class="card">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i> Add New Product
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if($message): ?>
                        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                            <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Product Name -->
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Product Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    
                                    <!-- Category -->
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category *</label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php foreach($categories as $cat): ?>
                                            <option value="<?= $cat['category_id'] ?>">
                                                <?= htmlspecialchars($cat['category_name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- Brand -->
                                    <div class="mb-3">
                                        <label for="brand" class="form-label">Brand</label>
                                        <input type="text" class="form-control" id="brand" name="brand">
                                    </div>
                                    
                                    <!-- Price -->
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price (Rs.) *</label>
                                        <input type="number" class="form-control" id="price" name="price" 
                                               step="0.01" min="0" required>
                                    </div>
                                    
                                    <!-- Stock Quantity -->
                                    <div class="mb-3">
                                        <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                        <input type="number" class="form-control" id="stock_quantity" 
                                               name="stock_quantity" min="0" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <!-- Image Upload -->
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Product Image</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <small class="text-muted">Accepted formats: JPG, PNG, GIF</small>
                                    </div>
                                    
                                    <!-- Specifications -->
                                    <div class="mb-3">
                                        <label class="form-label">Specifications</label>
                                        <input type="text" class="form-control mb-2" id="spec_1" name="spec_1" 
                                               placeholder="Specification 1">
                                        <input type="text" class="form-control mb-2" id="spec_2" name="spec_2" 
                                               placeholder="Specification 2">
                                        <input type="text" class="form-control" id="spec_3" name="spec_3" 
                                               placeholder="Specification 3">
                                    </div>
                                    
                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description *</label>
                                        <textarea class="form-control" id="description" name="description" 
                                                  rows="4" required></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="text-end mt-4">
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Add Product
                                </button>
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