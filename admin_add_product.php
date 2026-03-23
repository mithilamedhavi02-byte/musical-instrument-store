<?php
/**
 * admin_add_product.php
 * Professional Admin Dashboard - Product Management
 */
session_start();
require_once "../db.php";

// 1. Strict Admin Authentication
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

// Get categories for dropdown - Using standard error handling
$categories_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");

// 2. Form Processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize all inputs
    $name        = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $price       = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $stock       = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
    $is_digital  = isset($_POST['is_digital']) ? 1 : 0;
    $image_url   = filter_input(INPUT_POST, 'image_url', FILTER_VALIDATE_URL);

    // Advanced Validation
    if (!$name || $price === false || $stock === false) {
        $error = "Please fill all required fields with valid data.";
    } elseif ($price <= 0) {
        $error = "Price must be a positive value.";
    } elseif ($stock < 0 && !$is_digital) {
        $error = "Physical products cannot have negative stock.";
    } else {
        // Digital products stock management
        $final_stock = $is_digital ? 0 : $stock;

        $query = "INSERT INTO products(name, price, stock, category_id, is_digital, description, image_url, created_at) 
                  VALUES(?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sdiiiss", $name, $price, $final_stock, $category_id, $is_digital, $description, $image_url);

        if (mysqli_stmt_execute($stmt)) {
            $success = "New product '{$name}' added to inventory successfully!";
            // Clear inputs for next entry
            unset($_POST);
        } else {
            $error = "Database Error: Could not save product.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Add Product - Music Shop</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --admin-primary: #6366f1;
            --admin-secondary: #4f46e5;
            --sidebar-bg: #1e1b4b;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Sidebar Styling */
        .sidebar {
            background-color: var(--sidebar-bg);
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 280px;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .nav-link {
            color: #94a3b8;
            padding: 14px 24px;
            font-weight: 500;
            transition: all 0.3s;
            border-radius: 12px;
            margin: 4px 15px;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(99, 102, 241, 0.2);
        }

        .nav-link i { width: 25px; }

        /* Content Area */
        .main-content {
            margin-left: 280px;
            padding: 40px;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .form-label { font-weight: 600; font-size: 0.9rem; color: #475569; }
        
        .form-control, .form-select {
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background-color: #fcfcfd;
        }

        .form-control:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-add {
            background: var(--admin-primary);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-add:hover {
            background: var(--admin-secondary);
            transform: translateY(-2px);
        }

        .stat-card-mini {
            background: #fff;
            padding: 20px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        @media (max-width: 992px) {
            .sidebar { width: 100%; position: relative; min-height: auto; }
            .main-content { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>

<div class="d-flex flex-column flex-lg-row">
    <aside class="sidebar py-4">
        <div class="px-4 mb-4">
            <h5 class="fw-bold d-flex align-items-center">
                <span class="p-2 bg-primary rounded-3 me-2"><i class="fas fa-music text-white fa-sm"></i></span>
                CoreAdmin
            </h5>
        </div>
        
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Overview</a>
            <a href="manage_products.php" class="nav-link active"><i class="fas fa-box"></i> Inventory</a>
            <a href="orders.php" class="nav-link"><i class="fas fa-shopping-bag"></i> Orders</a>
            <a href="users.php" class="nav-link"><i class="fas fa-user-friends"></i> Customers</a>
            <hr class="mx-4 opacity-10">
            <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-power-off"></i> Logout</a>
        </nav>
    </aside>

    <main class="main-content flex-grow-1">
        <header class="mb-5 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Add New Product</h2>
                <p class="text-muted">Fill in the details to expand your catalog.</p>
            </div>
            <div class="d-none d-md-block">
                <span class="badge bg-light text-dark p-2 border"><?php echo date('D, d M Y'); ?></span>
            </div>
        </header>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card shadow-sm p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 rounded-4 d-flex align-items-center">
                            <i class="fas fa-circle-xmark me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success border-0 rounded-4 d-flex align-items-center">
                            <i class="fas fa-circle-check me-2"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="productForm">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Product Title</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Yamaha F310 Acoustic Guitar" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Base Price ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">$</span>
                                    <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Initial Stock</label>
                                <input type="number" name="stock" id="stockField" class="form-control" value="10" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                                        <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Product Description</label>
                                <textarea name="description" class="form-control" rows="5" placeholder="Highlight key features..."></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Media URL (CDN/Link)</label>
                                <input type="url" name="image_url" class="form-control" placeholder="https://image-server.com/photo.jpg">
                            </div>

                            <div class="col-12 mt-4">
                                <div class="p-3 bg-light rounded-4 border d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold">Digital Fulfillment</h6>
                                        <small class="text-muted">Is this a downloadable file or virtual product?</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_digital" id="isDigitalToggle">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4 border-top pt-4">
                                <button type="submit" class="btn btn-add">
                                    <i class="fas fa-plus me-2"></i>Publish Product
                                </button>
                                <a href="manage_products.php" class="btn btn-link text-muted">Discard</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="stat-card-mini border shadow-sm">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-boxes-stacked text-primary"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Live Inventory</small>
                        <span class="h5 fw-bold mb-0">942 Items</span>
                    </div>
                </div>

                <div class="card bg-dark text-white p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>Pro-Tip</h5>
                    <p class="small opacity-75">
                        High-quality descriptions increase conversion by 30%. Mention brand warranty and wood types for instruments.
                    </p>
                    <hr class="opacity-25">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: 75%"></div>
                            </div>
                            <small class="mt-2 d-block opacity-50">Storage Limit: 75%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Intelligent stock management for digital products
    const toggle = document.getElementById('isDigitalToggle');
    const stockField = document.getElementById('stockField');

    toggle.addEventListener('change', function() {
        if(this.checked) {
            stockField.value = '0';
            stockField.disabled = true;
            stockField.classList.add('bg-light');
        } else {
            stockField.disabled = false;
            stockField.classList.remove('bg-light');
            stockField.value = '10';
        }
    });
</script>
</body>
</html>