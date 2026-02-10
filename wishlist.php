<?php
// wishlist.php
session_start();
require_once "includes/db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = 'wishlist.php';
    header("Location: login.php?redirect_from=wishlist");
    exit();
}

// Initialize wishlist if not exists
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'remove_from_wishlist' && isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        
        if (($key = array_search($product_id, $_SESSION['wishlist'])) !== false) {
            unset($_SESSION['wishlist'][$key]);
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // Re-index
            
            echo json_encode([
                'success' => true, 
                'message' => 'Removed from wishlist',
                'wishlist_count' => count($_SESSION['wishlist'])
            ]);
            exit;
        }
        
        echo json_encode(['success' => false, 'message' => 'Product not found in wishlist']);
        exit;
    }
    
    if ($_POST['action'] === 'move_to_cart' && isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        
        // Add to cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += 1;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }
        
        // Remove from wishlist
        if (($key = array_search($product_id, $_SESSION['wishlist'])) !== false) {
            unset($_SESSION['wishlist'][$key]);
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Moved to cart successfully!',
            'wishlist_count' => count($_SESSION['wishlist']),
            'cart_count' => count($_SESSION['cart'])
        ]);
        exit;
    }
}

// Get wishlist products
$wishlist_products = [];
$total_items = 0;
$total_price = 0;

if (!empty($_SESSION['wishlist'])) {
    $product_ids = $_SESSION['wishlist'];
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id IN (" . implode(',', $product_ids) . ")";
    
    $result = query($sql);
    $wishlist_products = $result ? fetch_all($result) : [];
    
    // Calculate totals
    foreach($wishlist_products as $product) {
        $total_items++;
        $total_price += $product['price'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - The Music Shop</title>
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

        .wishlist-header {
            background: linear-gradient(rgba(0,0,0,0.9), rgba(0,0,0,0.9)), 
                        url('https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            padding: 60px 0;
            color: var(--gold);
            text-align: center;
            border-bottom: 4px solid var(--gold);
        }

        .wishlist-card {
            border: 1px solid rgba(212, 175, 55, 0.2);
            background: var(--white);
            border-radius: 0;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .wishlist-card:hover {
            border-color: var(--gold);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .wishlist-img-container {
            height: 200px;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .wishlist-img {
            max-height: 80%;
            max-width: 80%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .wishlist-card:hover .wishlist-img {
            transform: scale(1.05);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-move-to-cart {
            background: var(--black);
            color: var(--gold);
            border: 1px solid var(--gold);
            border-radius: 0;
            flex: 1;
            transition: 0.3s;
        }

        .btn-move-to-cart:hover {
            background: var(--gold);
            color: var(--black);
        }

        .btn-remove-wishlist {
            background: transparent;
            color: #dc3545;
            border: 1px solid #dc3545;
            border-radius: 0;
            width: 40px;
            transition: 0.3s;
        }

        .btn-remove-wishlist:hover {
            background: #dc3545;
            color: white;
        }

        .wishlist-summary {
            background: var(--light-gray);
            padding: 25px;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .empty-wishlist {
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .wishlist-badge {
            background-color: var(--gold) !important;
            color: var(--black) !important;
        }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <header class="wishlist-header">
        <div class="container">
            <h1 class="mb-3" style="font-family: 'Playfair Display', serif;">My Wishlist</h1>
            <p class="mb-0">Your curated collection of musical masterpieces</p>
        </div>
    </header>
    
    <main class="container py-5">
        <?php if(empty($wishlist_products)): ?>
        <div class="empty-wishlist">
            <div class="col-md-6 mx-auto">
                <i class="far fa-heart fa-4x text-muted mb-4"></i>
                <h3 class="fw-bold mb-3">Your Wishlist Awaits</h3>
                <p class="text-muted mb-4">Save your favorite products to easily access them later</p>
                <div class="d-grid gap-2 d-md-block">
                    <a href="products.php" class="btn btn-gold px-4 py-2">
                        <i class="fas fa-guitar me-2"></i> Browse Collection
                    </a>
                </div>
            </div>
        </div>
        <?php else: ?>
        
        <div class="row mb-4">
            <div class="col-md-8">
                <h4 class="fw-bold">
                    <i class="fas fa-heart text-danger me-2"></i>
                    My Saved Items (<?= $total_items ?>)
                </h4>
            </div>
            <div class="col-md-4 text-end">
                <a href="products.php" class="btn btn-outline-dark">
                    <i class="fas fa-plus me-2"></i> Add More Items
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-9">
                <div class="row g-4">
                    <?php 
                    // Define images array (same as products.php)
                    $product_images = [
                        1 => 'guitar1.jpg',
                        2 => 'piano1.jpg',
                        3 => 'drums1.jpg',
                        4 => 'violin1.jpg',
                        5 => 'saxophone1.jpg',
                        6 => 'trumpet1.jpg',
                        7 => 'flute1.jpg',
                        8 => 'cello1.jpg',
                        9 => 'keyboard1.jpg',
                        10 => 'bass1.jpg',
                        11 => 'harp1.jpg',
                        12 => 'ukulele1.jpg',
                    ];
                    
                    foreach($wishlist_products as $product): 
                        // Get product image
                        $image_path = 'assets/images/products/';
                        $default_image = $image_path . 'default.jpg';
                        $image_file = $default_image;
                        
                        if (isset($product_images[$product['product_id']])) {
                            $potential_image = $image_path . $product_images[$product['product_id']];
                            if (file_exists($potential_image)) {
                                $image_file = $potential_image;
                            }
                        }
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card wishlist-card" id="wishlist-item-<?= $product['product_id'] ?>">
                            <div class="wishlist-img-container">
                                <img src="<?= htmlspecialchars($image_file) ?>" 
                                     class="wishlist-img" 
                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                     onerror="this.onerror=null; this.src='<?= $default_image ?>';">
                            </div>
                            
                            <div class="card-body">
                                <p class="text-muted small mb-1 text-uppercase">
                                    <?= htmlspecialchars($product['category_name']) ?>
                                </p>
                                <h6 class="card-title fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h6>
                                <p class="price-tag mb-3">Rs. <?= number_format($product['price'], 2) ?></p>
                                
                                <div class="action-buttons">
                                    <button class="btn btn-move-to-cart" 
                                            onclick="moveToCart(<?= $product['product_id'] ?>)">
                                        <i class="fas fa-cart-plus me-1"></i> Move to Cart
                                    </button>
                                    <button class="btn btn-remove-wishlist" 
                                            onclick="removeFromWishlist(<?= $product['product_id'] ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <div class="mt-3 text-center">
                                    <a href="product_detail.php?id=<?= $product['product_id'] ?>" 
                                       class="text-decoration-none small text-dark">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="wishlist-summary">
                    <h5 class="fw-bold mb-4">Wishlist Summary</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Items:</span>
                        <span class="fw-bold"><?= $total_items ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Value:</span>
                        <span class="fw-bold text-success">Rs. <?= number_format($total_price, 2) ?></span>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-grid gap-2">
                        <a href="products.php?category=<?= $wishlist_products[0]['category_id'] ?? 0 ?>" 
                           class="btn btn-outline-dark">
                            <i class="fas fa-guitar me-2"></i> Similar Products
                        </a>
                        
                        <?php if(!empty($wishlist_products)): ?>
                        <button class="btn btn-gold" onclick="moveAllToCart()">
                            <i class="fas fa-shopping-cart me-2"></i> Move All to Cart
                        </button>
                        <?php endif; ?>
                        
                        <a href="products.php" class="btn btn-dark">
                            <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
                        </a>
                    </div>
                    
                    <div class="mt-4">
                        <p class="small text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Items remain in your wishlist until you remove them or move to cart
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>
    
    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer">
        <!-- Toasts will be inserted here -->
    </div>
    
    <?php include "includes/footer.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Show toast notification
    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer');
        const toastId = 'toast-' + Date.now();
        
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 3000
        });
        toast.show();
        
        // Remove from DOM after hide
        toastElement.addEventListener('hidden.bs.toast', function () {
            this.remove();
        });
    }

    // Remove from wishlist
    async function removeFromWishlist(productId) {
        if (!confirm('Remove this item from your wishlist?')) return;
        
        try {
            const response = await fetch('wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove_from_wishlist&product_id=${productId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Remove item from DOM
                const itemElement = document.getElementById(`wishlist-item-${productId}`);
                if (itemElement) {
                    itemElement.style.opacity = '0.5';
                    itemElement.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        itemElement.remove();
                        // Check if wishlist is empty
                        if (document.querySelectorAll('.wishlist-card').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
                
                showToast(data.message, 'success');
                
                // Update wishlist count in navbar
                const wishlistBadges = document.querySelectorAll('.wishlist-count');
                wishlistBadges.forEach(badge => {
                    badge.textContent = data.wishlist_count;
                    if (data.wishlist_count == 0) {
                        badge.style.display = 'none';
                    }
                });
            } else {
                showToast(data.message, 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error removing item', 'danger');
        }
    }

    // Move to cart
    async function moveToCart(productId) {
        try {
            const response = await fetch('wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=move_to_cart&product_id=${productId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Remove item from DOM
                const itemElement = document.getElementById(`wishlist-item-${productId}`);
                if (itemElement) {
                    itemElement.style.opacity = '0.5';
                    itemElement.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        itemElement.remove();
                        // Check if wishlist is empty
                        if (document.querySelectorAll('.wishlist-card').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
                
                showToast(data.message, 'success');
                
                // Update counts in navbar
                const cartBadges = document.querySelectorAll('.cart-count');
                cartBadges.forEach(badge => {
                    badge.textContent = data.cart_count;
                    badge.style.display = 'inline-block';
                });
                
                const wishlistBadges = document.querySelectorAll('.wishlist-count');
                wishlistBadges.forEach(badge => {
                    badge.textContent = data.wishlist_count;
                    if (data.wishlist_count == 0) {
                        badge.style.display = 'none';
                    }
                });
            } else {
                showToast(data.message, 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error moving to cart', 'danger');
        }
    }

    // Move all to cart
    async function moveAllToCart() {
        if (!confirm('Move all items from wishlist to cart?')) return;
        
        // Get all product IDs from wishlist items
        const productCards = document.querySelectorAll('.wishlist-card');
        const productIds = [];
        
        productCards.forEach(card => {
            const id = card.id.replace('wishlist-item-', '');
            if (id) productIds.push(parseInt(id));
        });
        
        // Move each item to cart
        for (const productId of productIds) {
            try {
                const response = await fetch('wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=move_to_cart&product_id=${productId}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update counts
                    const cartBadges = document.querySelectorAll('.cart-count');
                    cartBadges.forEach(badge => {
                        badge.textContent = data.cart_count;
                    });
                    
                    const wishlistBadges = document.querySelectorAll('.wishlist-count');
                    wishlistBadges.forEach(badge => {
                        badge.textContent = data.wishlist_count;
                    });
                }
            } catch (error) {
                console.error('Error moving product:', productId, error);
            }
        }
        
        // Reload page after all items are moved
        showToast('All items moved to cart!', 'success');
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
    </script>
</body>
</html>