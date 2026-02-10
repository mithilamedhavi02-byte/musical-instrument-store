<?php
// category.php
session_start();
require_once "includes/db.php";

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 15; // Category එකකට උපරිම 15 products
$offset = ($page - 1) * $limit;

// Get category details
$cat_query = "SELECT * FROM categories WHERE category_id = ?";
$cat_stmt = db_query($cat_query, [$category_id]);
$category = db_fetch_one($cat_stmt);

if(!$category) {
    header("Location: products.php");
    exit();
}

// Get total products in category
$count_query = "SELECT COUNT(*) as total FROM products WHERE category_id = ?";
$count_stmt = db_query($count_query, [$category_id]);
$total_row = db_fetch_one($count_stmt);
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $limit);

// Get products for this category
$query = "SELECT p.*, c.category_name FROM products p 
          JOIN categories c ON p.category_id = c.category_id 
          WHERE p.category_id = ? 
          ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
          
$stmt = db_query($query, [$category_id, $limit, $offset]);
$products = db_fetch_all($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $category['category_name'] ?> - The Music Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <div class="container py-5">
        <!-- Category Header -->
        <div class="row mb-5">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                        <li class="breadcrumb-item active"><?= $category['category_name'] ?></li>
                    </ol>
                </nav>
                <h1 class="fw-bold mb-3"><?= $category['category_name'] ?></h1>
                <p class="text-muted"><?= $total_products ?> products available</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="products.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Back to All Products
                </a>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="row g-4">
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $product): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card product-card h-100">
                        <img src="<?= $product['image_url'] ?: 'https://via.placeholder.com/300' ?>" 
                             class="card-img-top" alt="<?= $product['name'] ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title fw-bold"><?= $product['name'] ?></h6>
                            <p class="text-muted small mb-2"><?= $product['category_name'] ?></p>
                            <h5 class="text-primary fw-bold mt-auto">Rs. <?= number_format($product['price'], 2) ?></h5>
                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-dark btn-sm" onclick="addToCart(<?= $product['product_id'] ?>)">
                                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                </button>
                                <a href="product_detail.php?id=<?= $product['product_id'] ?>" class="btn btn-outline-primary btn-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-music fa-3x text-muted mb-3"></i>
                    <h4>No products in this category</h4>
                    <p class="text-muted">Check back soon for new arrivals</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav class="mt-5">
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="category.php?id=<?= $category_id ?>&page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
    
    <?php include "includes/footer.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function addToCart(productId) {
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'product_id=' + productId + '&action=add'
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Added to cart!');
            }
        });
    }
    </script>
</body>
</html>