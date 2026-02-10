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

// Remove from wishlist
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    $index = array_search($product_id, $_SESSION['wishlist']);
    if ($index !== false) {
        array_splice($_SESSION['wishlist'], $index, 1);
    }
    header("Location: wishlist.php");
    exit();
}

// Get wishlist products
$wishlist_products = [];
if (!empty($_SESSION['wishlist'])) {
    $product_ids = $_SESSION['wishlist'];
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id IN (" . implode(',', $product_ids) . ")";
    
    $result = query($sql);
    $wishlist_products = $result ? fetch_all($result) : [];
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
        .wishlist-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: 0.3s;
            position: relative;
        }
        .wishlist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            color: #ff4757;
            text-decoration: none;
            transition: 0.3s;
            z-index: 10;
        }
        .remove-btn:hover {
            background: #ff4757;
            color: white;
            transform: scale(1.1);
        }
        .empty-wishlist {
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <div class="container py-5">
        <h1 class="fw-bold mb-4">My Wishlist</h1>
        
        <?php if(empty($wishlist_products)): ?>
        <div class="empty-wishlist">
            <div class="text-center">
                <i class="far fa-heart fa-4x text-muted mb-4"></i>
                <h3>Your wishlist is empty</h3>
                <p class="text-muted mb-4">Save your favorite products here</p>
                <a href="products.php" class="btn btn-primary btn-lg">Browse Products</a>
            </div>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach($wishlist_products as $product): ?>
            <div class="col-md-4 col-lg-3">
                <div class="card wishlist-card h-100">
                    <a href="wishlist.php?remove=<?= $product['product_id'] ?>" 
                       class="remove-btn" 
                       onclick="return confirm('Remove from wishlist?')">
                        <i class="fas fa-times"></i>
                    </a>
                    
                    <img src="<?= htmlspecialchars($product['image_url'] ?: 'https://via.placeholder.com/300') ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         style="height: 200px; object-fit: cover;">
                    
                    <div class="card-body">
                        <h6 class="card-title fw-bold"><?= htmlspecialchars($product['name']) ?></h6>
                        <p class="text-muted small"><?= htmlspecialchars($product['category_name']) ?></p>
                        <h5 class="text-primary fw-bold">Rs. <?= number_format($product['price'], 2) ?></h5>
                        
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-dark" onclick="addToCart(<?= $product['product_id'] ?>)">
                                <i class="fas fa-cart-plus me-1"></i> Add to Cart
                            </button>
                            <a href="product_detail.php?id=<?= $product['product_id'] ?>" 
                               class="btn btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <?php include "includes/footer.php"; ?>
    
    <script>
    function addToCart(productId) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('action', 'add');
        
        fetch('add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                // Update cart badge
                const cartBadge = document.querySelector('a[href="cart.php"] .badge');
                if(cartBadge) {
                    cartBadge.textContent = data.cart_count || 0;
                }
            } else {
                if(data.requires_login) {
                    if(confirm('You need to login first. Do you want to login now?')) {
                        window.location.href = data.redirect;
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>