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
        
        // Add quantity support - store as associative array
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += 1;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }
        
        echo json_encode(['success' => true, 'message' => 'Added to cart!']);
        exit;
    }
    
    if ($_POST['action'] === 'toggle_wishlist' && isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        
        if (($key = array_search($product_id, $_SESSION['wishlist'])) !== false) {
            unset($_SESSION['wishlist'][$key]);
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // Re-index
            echo json_encode(['success' => true, 'in_wishlist' => false, 'message' => 'Removed from wishlist']);
        } else {
            $_SESSION['wishlist'][] = $product_id;
            echo json_encode(['success' => true, 'in_wishlist' => true, 'message' => 'Added to wishlist!']);
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

// Build SQL query
$where_conditions = [];
if ($category_id > 0) { $where_conditions[] = "p.category_id = $category_id"; }
if (!empty($search)) { $where_conditions[] = "(p.name LIKE '%$search%' OR p.description LIKE '%$search%')"; }
$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : '';

// Get total products count
$count_sql = "SELECT COUNT(*) as total FROM products p $where_clause";
$count_result = query($count_sql);
$total_data = $count_result ? fetch_one($count_result) : ['total' => 0];
$total_products = $total_data['total'];
$total_pages = ceil($total_products / $limit);

// Get products
$products_sql = "SELECT p.*, c.category_name FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 $where_clause ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";
$products_result = query($products_sql);
$products = $products_result ? fetch_all($products_result) : [];

// Get categories
$categories_result = query("SELECT * FROM categories ORDER BY category_name");
$categories = $categories_result ? fetch_all($categories_result) : [];

// Calculate cart total items
$cart_total_items = array_sum($_SESSION['cart']);
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
            border-radius: 0;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .product-card:hover {
            border-color: var(--gold);
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.05);
        }

        .product-img-container {
            height: 250px;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-img {
            max-height: 80%;
            max-width: 80%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.05);
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
            border-radius: 0;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            transition: 0.3s;
            padding: 12px 24px;
        }

        .btn-gold:hover {
            background: var(--gold);
            color: var(--black);
        }

        .pagination .page-link {
            color: var(--black);
            border: 1px solid var(--gold);
            margin: 0 2px;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--gold);
            border-color: var(--gold);
            color: var(--black);
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
            border-radius: 0;
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
                <?php 
                // Define your images array here
                $product_images = [
                    1 => 'image1.jpg',   // Product ID 1
                    2 => 'image2.jpg',   // Product ID 2
                    3 => 'image3.jpg',   // Product ID 3
                    4 => 'image4.jpg',   // Product ID 4
                    5 => 'image5.jpg',   // Product ID 5
                    6 => 'image6.jpg',   // Product ID 6
                    7 => 'image7.jpg',   // Product ID 7
                    8 => 'image8.jpg',   // Product ID 8
                    9 => 'image9.jpg',   // Product ID 9
                    10 => 'image10.jpg', // Product ID 10
                    11 => 'image11.jpg', // Product ID 11
                    12 => 'image12.jpg', // Product ID 12
                    // Add more as needed
                ];
                
                foreach($products as $product): 
                    $is_in_wishlist = in_array($product['product_id'], $_SESSION['wishlist']);
                    
                    // Get product image from your array
                    $image_path = 'assets/images/products/';
                    $default_image = $image_path . 'default.jpg';
                    
                    // Check if image exists in your array
                    if (isset($product_images[$product['product_id']])) {
                        $image_file = $image_path . $product_images[$product['product_id']];
                    } else {
                        // If not in array, use a generic naming pattern
                        $generic_image = 'image' . $product['product_id'] . '.jpg';
                        if (file_exists($image_path . $generic_image)) {
                            $image_file = $image_path . $generic_image;
                        } else {
                            $image_file = $default_image;
                        }
                    }
                    
                    // If the file doesn't exist, use default
                    if (!file_exists($image_file)) {
                        $image_file = $default_image;
                    }
                ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card product-card h-100">
                        <button class="wishlist-btn <?= $is_in_wishlist ? 'active' : '' ?>" 
                                onclick="toggleWishlist(<?= $product['product_id'] ?>, this)">
                            <i class="<?= $is_in_wishlist ? 'fas' : 'far' ?> fa-heart"></i>
                        </button>
                        
                        <div class="product-img-container">
                            <img src="<?= htmlspecialchars($image_file) ?>" 
                                 class="product-img" 
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 onerror="this.onerror=null; this.src='<?= $default_image ?>';">
                        </div>
                        
                        <div class="card-body text-center">
                            <p class="text-muted small mb-1 text-uppercase"><?= htmlspecialchars($product['category_name']) ?></p>
                            <h6 class="card-title fw-bold mb-3"><?= htmlspecialchars($product['name']) ?></h6>
                            <p class="price-tag mb-4">Rs. <?= number_format($product['price'], 2) ?></p>
                            
                            <div class="d-grid gap-2">
                                <button class="btn btn-gold py-2" onclick="addToCart(<?= $product['product_id'] ?>)">
                                    <i class="fas fa-shopping-bag me-2"></i> Add to Cart
                                </button>
                                <a href="product_detail.php?id=<?= $product['product_id'] ?>" 
                                   class="btn btn-link text-dark text-decoration-none small">
                                    View Masterpiece
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
                <!-- Previous button -->
                <?php if($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page-1 ?>&category=<?= $category_id ?>&search=<?= urlencode($search) ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Page numbers -->
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
                
                <!-- Next button -->
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
        
        // Type-based styling
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
        
        // Trigger animation
        setTimeout(() => {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.add('show');
            }
        }, 10);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }
        }, 3000);
    }

    // Update cart count in navbar
    function updateCartCount(change = 1) {
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            let currentCount = parseInt(element.textContent) || 0;
            element.textContent = currentCount + change;
            element.style.display = 'inline-block';
        });
    }

    // Update wishlist count in navbar
    function updateWishlistCount(change = 1) {
        const wishlistCountElements = document.querySelectorAll('.wishlist-count');
        wishlistCountElements.forEach(element => {
            let currentCount = parseInt(element.textContent) || 0;
            element.textContent = Math.max(0, currentCount + change);
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
                updateCartCount(1);
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
                    updateWishlistCount(1);
                } else {
                    button.classList.remove('active');
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    showToast(data.message, 'info');
                    updateWishlistCount(-1);
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
        updateCartCount(0);
        
        // Update wishlist count on page load
        updateWishlistCount(0);
        
        // Add click effect to buttons
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