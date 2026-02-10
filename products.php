<?php
// products.php - PROFESSIONAL LUXURY VERSION
session_start();
require_once "includes/db.php";

// Initialize sessions
if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
if (!isset($_SESSION['wishlist'])) { $_SESSION['wishlist'] = []; }

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'add_to_cart' && isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        
        // Check if user is logged in for cart
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'Please login to add to cart!',
                'requires_login' => true,
                'redirect' => 'login.php'
            ]);
            exit;
        }
        
        // Add quantity support
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += 1;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Added to cart!',
            'cart_count' => count($_SESSION['cart'])
        ]);
        exit;
    }
    
    if ($_POST['action'] === 'toggle_wishlist' && isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        
        // Check if user is logged in for wishlist
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'Please login to add to wishlist!',
                'requires_login' => true,
                'redirect' => 'login.php'
            ]);
            exit;
        }
        
        if (($key = array_search($product_id, $_SESSION['wishlist'])) !== false) {
            unset($_SESSION['wishlist'][$key]);
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // Re-index
            echo json_encode([
                'success' => true, 
                'in_wishlist' => false, 
                'message' => 'Removed from wishlist',
                'wishlist_count' => count($_SESSION['wishlist'])
            ]);
        } else {
            $_SESSION['wishlist'][] = $product_id;
            echo json_encode([
                'success' => true, 
                'in_wishlist' => true, 
                'message' => 'Added to wishlist!',
                'wishlist_count' => count($_SESSION['wishlist'])
            ]);
        }
        exit;
    }
}

// Filter parameters
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? escape(trim($_GET['search'])) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build SQL query - Only show active products
$where_conditions = ["p.active = 1"];
if ($category_id > 0) { 
    $where_conditions[] = "p.category_id = $category_id"; 
}
if (!empty($search)) { 
    $where_conditions[] = "(p.name LIKE '%$search%' OR p.description LIKE '%$search%' OR p.brand LIKE '%$search%')"; 
}
$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// Get total products count
$count_sql = "SELECT COUNT(*) as total FROM products p $where_clause";
$count_result = query($count_sql);
$total_data = $count_result ? fetch_one($count_result) : ['total' => 0];
$total_products = $total_data['total'];
$total_pages = ceil($total_products / $limit);

// Get products with categories - Only active products
$products_sql = "SELECT p.*, c.category_name FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 $where_clause 
                 ORDER BY p.created_at DESC 
                 LIMIT $limit OFFSET $offset";
$products_result = query($products_sql);
$products = $products_result ? fetch_all($products_result) : [];

// Get categories - Only active categories
$categories_result = query("SELECT * FROM categories WHERE active = 1 ORDER BY category_name");
$categories = $categories_result ? fetch_all($categories_result) : [];

// Calculate cart total items
$cart_total_items = array_sum($_SESSION['cart']);
$wishlist_count = count($_SESSION['wishlist']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Collection | The Music Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --gold: #D4AF37;
            --dark-gold: #B8860B;
            --black: #0A0A0A;
            --white: #FFFFFF;
            --light-gray: #F9F9F9;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--white);
            color: var(--black);
        }

        /* --- Header: Black & Gold --- */
        .navbar {
            background: var(--black) !important;
            border-bottom: 2px solid var(--gold);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            color: var(--gold) !important;
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
        }
        
        .nav-link {
            color: var(--white) !important;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: var(--gold) !important;
        }
        
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            padding: 100px 0 60px 0;
            color: var(--gold);
            text-align: center;
            border-bottom: 4px solid var(--gold);
        }

        .hero-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            letter-spacing: 2px;
        }

        /* --- Body: White & Gold --- */
        .category-pill {
            border: 1px solid var(--gold);
            color: var(--black);
            background: transparent;
            border-radius: 50px;
            padding: 8px 20px;
            margin: 5px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .category-pill:hover, .category-pill.active {
            background: var(--gold);
            color: var(--black) !important;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .product-card {
            border: 1px solid rgba(212, 175, 55, 0.2);
            background: var(--white);
            border-radius: 10px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .product-card:hover {
            border-color: var(--gold);
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .product-img-container {
            height: 250px;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 20px;
        }

        .product-img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.05);
        }

        /* Stock Status */
        .stock-status {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 5;
        }
        
        .in-stock {
            background: rgba(40, 167, 69, 0.9);
            color: white;
        }
        
        .low-stock {
            background: rgba(255, 193, 7, 0.9);
            color: black;
        }
        
        .out-stock {
            background: rgba(220, 53, 69, 0.9);
            color: white;
        }

        .wishlist-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--white);
            border: 1px solid var(--gold);
            color: var(--gold);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: 0.3s;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wishlist-btn:hover, .wishlist-btn.active {
            background: var(--gold);
            color: var(--black);
            transform: scale(1.1);
        }

        .price-tag {
            color: var(--dark-gold);
            font-weight: 700;
            font-size: 1.25rem;
        }

        .btn-gold {
            background: var(--black);
            color: var(--gold);
            border: 1px solid var(--gold);
            border-radius: 5px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            transition: 0.3s;
            padding: 12px 24px;
            width: 100%;
        }

        .btn-gold:hover {
            background: var(--gold);
            color: var(--black);
        }
        
        .btn-gold:disabled {
            background: #ccc;
            border-color: #ccc;
            color: #666;
            cursor: not-allowed;
        }

        .pagination .page-link {
            color: var(--black);
            border: 1px solid var(--gold);
            margin: 0 2px;
            border-radius: 5px;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--gold);
            border-color: var(--gold);
            color: var(--black);
        }

        /* Product Details */
        .product-brand {
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .product-specs {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 15px;
        }
        
        .spec-item {
            display: flex;
            align-items: center;
            margin-bottom: 3px;
        }
        
        .spec-item i {
            margin-right: 5px;
            color: var(--gold);
            font-size: 0.8rem;
        }

        /* Toast notifications */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
        }

        .custom-toast {
            background: var(--black);
            color: var(--gold);
            border: 1px solid var(--gold);
            border-radius: 5px;
            min-width: 300px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .custom-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .cart-badge, .wishlist-badge {
            background-color: var(--gold) !important;
            color: var(--black) !important;
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--gold);
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Form styling */
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }
        
        /* Cart Status */
        .cart-status {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <?php include "includes/navbar.php"; ?>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <header class="hero-section">
        <div class="container">
            <h1 class="mb-3">Masterpiece Collection</h1>
            <p class="text-uppercase tracking-widest" style="letter-spacing: 3px;">Elevate Your Sound with Excellence</p>
        </div>
    </header>

    <main class="container py-5">
        
        <div class="row mb-5 justify-content-center">
            <div class="col-md-7">
                <form method="GET" class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search instruments..." value="<?= htmlspecialchars($search) ?>"
                           style="border-color: var(--gold); height: 50px;">
                    <?php if($category_id > 0): ?>
                    <input type="hidden" name="category" value="<?= $category_id ?>">
                    <?php endif; ?>
                    <button class="btn btn-gold px-4" type="submit">Discover</button>
                </form>
            </div>
        </div>

        <div class="text-center mb-5">
            <a href="products.php" class="category-pill <?= $category_id == 0 ? 'active' : '' ?>">All Collection</a>
            <?php foreach($categories as $cat): ?>
            <a href="products.php?category=<?= $cat['category_id'] ?>" 
               class="category-pill <?= $category_id == $cat['category_id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($cat['category_name']) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="row g-4">
            <?php if(!empty($products)): ?>
                <?php foreach($products as $product): 
                    $is_in_wishlist = in_array($product['product_id'], $_SESSION['wishlist']);
                    $cart_quantity = $_SESSION['cart'][$product['product_id']] ?? 0;
                    
                    // Determine stock status
                    $stock_class = 'in-stock';
                    $stock_text = 'In Stock';
                    if ($product['stock_quantity'] == 0) {
                        $stock_class = 'out-stock';
                        $stock_text = 'Out of Stock';
                    } elseif ($product['stock_quantity'] <= 5) {
                        $stock_class = 'low-stock';
                        $stock_text = 'Low Stock';
                    }
                    
                    // Get product image
                    $image_file = 'assets/images/products/product_' . $product['product_id'] . '.jpg';
                    if (!file_exists($image_file)) {
                        $image_file = 'assets/images/products/default.jpg';
                    }
                    
                    // Get specifications
                    $specs = [];
                    if (!empty($product['spec_1'])) $specs[] = $product['spec_1'];
                    if (!empty($product['spec_2'])) $specs[] = $product['spec_2'];
                    if (!empty($product['spec_3'])) $specs[] = $product['spec_3'];
                ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card product-card h-100">
                        <!-- Stock Status -->
                        <div class="stock-status <?= $stock_class ?>">
                            <?= $stock_text ?>
                        </div>
                        
                        <!-- Wishlist Button -->
                        <?php if(isset($_SESSION['user_id'])): ?>
                        <button class="wishlist-btn <?= $is_in_wishlist ? 'active' : '' ?>" 
                                onclick="toggleWishlist(<?= $product['product_id'] ?>, this)">
                            <i class="<?= $is_in_wishlist ? 'fas' : 'far' ?> fa-heart"></i>
                        </button>
                        <?php else: ?>
                        <button class="wishlist-btn" onclick="showLoginAlert('wishlist')">
                            <i class="far fa-heart"></i>
                        </button>
                        <?php endif; ?>
                        
                        <!-- Product Image -->
                        <div class="product-img-container">
                            <img src="<?= htmlspecialchars($image_file) ?>" 
                                 class="product-img" 
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 onerror="this.src='assets/images/products/default.jpg'">
                        </div>
                        
                        <div class="card-body">
                            <!-- Category -->
                            <p class="text-muted small mb-1 text-uppercase">
                                <?= htmlspecialchars($product['category_name']) ?>
                            </p>
                            
                            <!-- Product Name -->
                            <h6 class="card-title fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h6>
                            
                            <!-- Brand -->
                            <?php if(!empty($product['brand'])): ?>
                            <p class="product-brand mb-2">
                                <i class="fas fa-tag me-1"></i> <?= htmlspecialchars($product['brand']) ?>
                            </p>
                            <?php endif; ?>
                            
                            <!-- Specifications -->
                            <div class="product-specs">
                                <?php foreach(array_slice($specs, 0, 2) as $spec): ?>
                                <div class="spec-item">
                                    <i class="fas fa-check"></i>
                                    <span><?= htmlspecialchars($spec) ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Price -->
                            <p class="price-tag mb-3">Rs. <?= number_format($product['price'], 2) ?></p>
                            
                            <!-- Stock Info -->
                            <p class="small text-muted mb-2">
                                <i class="fas fa-box me-1"></i> 
                                <?= $product['stock_quantity'] ?> available
                            </p>
                            
                            <!-- Cart Status -->
                            <?php if($cart_quantity > 0): ?>
                            <div class="cart-status mb-3">
                                <i class="fas fa-check-circle me-1"></i>
                                In Cart: <?= $cart_quantity ?> item(s)
                            </div>
                            <?php endif; ?>
                            
                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                <?php if($product['stock_quantity'] > 0): ?>
                                    <?php if(isset($_SESSION['user_id'])): ?>
                                    <button class="btn btn-gold" onclick="addToCart(<?= $product['product_id'] ?>)">
                                        <i class="fas fa-shopping-bag me-2"></i> 
                                        <?= $cart_quantity > 0 ? 'Add More' : 'Add to Cart' ?>
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-gold" onclick="showLoginAlert('cart')">
                                        <i class="fas fa-shopping-bag me-2"></i> Add to Cart
                                    </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-gold" disabled>
                                        <i class="fas fa-bell me-2"></i> Notify When Available
                                    </button>
                                <?php endif; ?>
                                
                                <!-- View Details Button -->
                                <a href="product_detail.php?id=<?= $product['product_id'] ?>" 
                                   class="btn btn-outline-dark">
                                    <i class="fas fa-eye me-2"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-music fa-3x text-muted mb-3"></i>
                    <h4 class="fw-bold">The Stage is Empty</h4>
                    <p class="text-muted">No instruments matched your current selection.</p>
                    <a href="products.php" class="btn btn-gold mt-3">View All Products</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if($total_pages > 1): ?>
        <nav class="mt-5">
            <ul class="pagination justify-content-center">
                <?php if($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page-1 ?>&category=<?= $category_id ?>&search=<?= urlencode($search) ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                for($i = $start_page; $i <= $end_page; $i++): 
                ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&category=<?= $category_id ?>&search=<?= urlencode($search) ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
                
                <?php if($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page+1 ?>&category=<?= $category_id ?>&search=<?= urlencode($search) ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </main>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <?php include "includes/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Show loading overlay
    function showLoading() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    // Hide loading overlay
    function hideLoading() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    // Show toast notification
    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer');
        const toastId = 'toast-' + Date.now();
        
        const borderColor = 'var(--gold)';
        const textColor = 'var(--gold)';
        const bgColor = 'var(--black)';
        
        const toastHTML = `
            <div id="${toastId}" class="custom-toast" 
                 style="background: ${bgColor}; color: ${textColor}; border-color: ${borderColor};">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <span><i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>${message}</span>
                    <button type="button" class="btn-close btn-close-white" 
                            onclick="document.getElementById('${toastId}').classList.remove('show'); setTimeout(() => document.getElementById('${toastId}').remove(), 300);"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        
        setTimeout(() => {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.add('show');
            }
        }, 10);
        
        setTimeout(() => {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }
        }, 3000);
    }

    // Show login alert
    function showLoginAlert(type) {
        const action = type === 'cart' ? 'add to cart' : 'save to wishlist';
        if (confirm(`Please login to ${action}. Do you want to login now?`)) {
            window.location.href = `login.php?redirect=${encodeURIComponent(window.location.href)}`;
        }
    }

    // Update cart count
    function updateCartCount(count = null) {
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            if (count !== null) {
                element.textContent = count;
            } else {
                let currentCount = parseInt(element.textContent) || 0;
                element.textContent = currentCount + 1;
            }
            element.style.display = 'inline-block';
        });
    }

    // Update wishlist count
    function updateWishlistCount(count = null) {
        const wishlistCountElements = document.querySelectorAll('.wishlist-count');
        wishlistCountElements.forEach(element => {
            if (count !== null) {
                element.textContent = count;
            } else {
                let currentCount = parseInt(element.textContent) || 0;
                element.textContent = currentCount + 1;
            }
            if (element.textContent === '0') {
                element.style.display = 'none';
            } else {
                element.style.display = 'inline-block';
            }
        });
    }

    // Add to Cart function
    async function addToCart(productId) {
        showLoading();
        
        try {
            const response = await fetch('products.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add_to_cart&product_id=${productId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                updateCartCount(data.cart_count);
                
                // Update button text
                const button = event.target;
                button.innerHTML = `<i class="fas fa-check me-2"></i>Added to Cart`;
                button.disabled = true;
                
                // Reload after 1 second to show updated cart status
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else if (data.requires_login) {
                if (confirm(data.message + ' Do you want to login now?')) {
                    window.location.href = data.redirect;
                }
            } else {
                showToast(data.message, 'warning');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error adding to cart', 'error');
        } finally {
            hideLoading();
        }
    }

    // Toggle Wishlist function
    async function toggleWishlist(productId, button) {
        showLoading();
        
        try {
            const response = await fetch('products.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=toggle_wishlist&product_id=${productId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                const icon = button.querySelector('i');
                
                if (data.in_wishlist) {
                    button.classList.add('active');
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    showToast(data.message, 'success');
                } else {
                    button.classList.remove('active');
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    showToast(data.message, 'info');
                }
                
                updateWishlistCount(data.wishlist_count);
            } else if (data.requires_login) {
                if (confirm(data.message + ' Do you want to login now?')) {
                    window.location.href = data.redirect;
                }
            } else {
                showToast('Error updating wishlist', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error updating wishlist', 'error');
        } finally {
            hideLoading();
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Update cart count on page load
        <?php if(isset($_SESSION['cart'])): ?>
        updateCartCount(<?= count($_SESSION['cart']) ?>);
        <?php endif; ?>
        
        // Update wishlist count on page load
        <?php if(isset($_SESSION['wishlist'])): ?>
        updateWishlistCount(<?= count($_SESSION['wishlist']) ?>);
        <?php endif; ?>
        
        // Add click animation to buttons
        const buttons = document.querySelectorAll('.btn-gold, .wishlist-btn');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    });
    </script>
</body>
</html>