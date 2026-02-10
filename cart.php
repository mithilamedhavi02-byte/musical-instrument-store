<?php
// cart.php
session_start();
require_once "includes/db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = 'cart.php';
    header("Location: login.php?redirect_from=cart");
    exit();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'update_quantity' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Cart updated',
            'cart_count' => count($_SESSION['cart'])
        ]);
        exit;
    }
    
    if ($_POST['action'] === 'remove_item' && isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Item removed',
            'cart_count' => count($_SESSION['cart'])
        ]);
        exit;
    }
    
    if ($_POST['action'] === 'move_to_wishlist' && isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        
        // Remove from cart
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        
        // Add to wishlist
        if (!in_array($product_id, $_SESSION['wishlist'])) {
            $_SESSION['wishlist'][] = $product_id;
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Moved to wishlist',
            'cart_count' => count($_SESSION['cart']),
            'wishlist_count' => count($_SESSION['wishlist'])
        ]);
        exit;
    }
}

// Get cart items with product details
$cart_items = [];
$subtotal = 0;
$total_items = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id IN (" . implode(',', $product_ids) . ")";
    
    $result = query($sql);
    $products = $result ? fetch_all($result) : [];
    
    // Organize cart items
    foreach ($products as $product) {
        $product_id = $product['product_id'];
        $quantity = $_SESSION['cart'][$product_id] ?? 1;
        $item_total = $product['price'] * $quantity;
        
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'item_total' => $item_total,
            'in_wishlist' => in_array($product_id, $_SESSION['wishlist'] ?? [])
        ];
        
        $total_items += $quantity;
        $subtotal += $item_total;
    }
}

$shipping = $subtotal > 5000 ? 0 : 500;
$tax = $subtotal * 0.05; // 5% tax
$grand_total = $subtotal + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - The Music Shop</title>
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

        .cart-header {
            background: linear-gradient(rgba(0,0,0,0.9), rgba(0,0,0,0.9)), 
                        url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            padding: 60px 0;
            color: var(--gold);
            text-align: center;
            border-bottom: 4px solid var(--gold);
        }

        .cart-table {
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .cart-table th {
            background: var(--light-gray);
            font-weight: 600;
            border-bottom: 2px solid var(--gold);
            padding: 15px;
        }

        .cart-table td {
            vertical-align: middle;
            padding: 20px 15px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }

        .product-img-cart {
            width: 80px;
            height: 80px;
            object-fit: contain;
            background: var(--light-gray);
            padding: 5px;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid var(--gold);
            background: transparent;
            color: var(--black);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
        }

        .quantity-btn:hover {
            background: var(--gold);
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid var(--gold);
            padding: 5px;
        }

        .cart-summary {
            background: var(--light-gray);
            padding: 25px;
            border: 1px solid rgba(212, 175, 55, 0.3);
            position: sticky;
            top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .summary-total {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--dark-gold);
        }

        .btn-checkout {
            background: var(--black);
            color: var(--gold);
            border: 2px solid var(--gold);
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
            width: 100%;
            margin-top: 20px;
        }

        .btn-checkout:hover {
            background: var(--gold);
            color: var(--black);
        }

        .empty-cart {
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .action-btn {
            padding: 5px 10px;
            font-size: 0.85rem;
            margin: 2px;
        }

        .move-to-wishlist {
            background: transparent;
            color: #dc3545;
            border: 1px solid #dc3545;
        }

        .move-to-wishlist:hover {
            background: #dc3545;
            color: white;
        }

        .remove-item {
            background: transparent;
            color: #6c757d;
            border: 1px solid #6c757d;
        }

        .remove-item:hover {
            background: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <header class="cart-header">
        <div class="container">
            <h1 class="mb-3" style="font-family: 'Playfair Display', serif;">Your Shopping Cart</h1>
            <p class="mb-0">Review your selected masterpieces</p>
        </div>
    </header>
    
    <main class="container py-5">
        <?php if(empty($cart_items)): ?>
        <div class="empty-cart">
            <div class="col-md-6 mx-auto">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h3 class="fw-bold mb-3">Your Cart is Empty</h3>
                <p class="text-muted mb-4">Add some musical instruments to your cart</p>
                <div class="d-grid gap-2 d-md-block">
                    <a href="products.php" class="btn btn-gold px-4 py-2 me-2">
                        <i class="fas fa-guitar me-2"></i> Browse Products
                    </a>
                    <a href="wishlist.php" class="btn btn-outline-dark px-4 py-2">
                        <i class="fas fa-heart me-2"></i> View Wishlist
                    </a>
                </div>
            </div>
        </div>
        <?php else: ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody">
                            <?php 
                            // Define images array
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
                            
                            foreach($cart_items as $index => $item): 
                                $product = $item['product'];
                                $product_id = $product['product_id'];
                                
                                // Get product image
                                $image_path = 'assets/images/products/';
                                $default_image = $image_path . 'default.jpg';
                                $image_file = $default_image;
                                
                                if (isset($product_images[$product_id])) {
                                    $potential_image = $image_path . $product_images[$product_id];
                                    if (file_exists($potential_image)) {
                                        $image_file = $potential_image;
                                    }
                                }
                            ?>
                            <tr id="cart-row-<?= $product_id ?>">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= htmlspecialchars($image_file) ?>" 
                                             class="product-img-cart me-3" 
                                             alt="<?= htmlspecialchars($product['name']) ?>"
                                             onerror="this.onerror=null; this.src='<?= $default_image ?>';">
                                        <div>
                                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($product['name']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($product['category_name']) ?></small>
                                            <?php if($item['in_wishlist']): ?>
                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <i class="fas fa-heart me-1"></i> In your wishlist
                                                </small>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="price-tag">Rs. <?= number_format($product['price'], 2) ?></span>
                                </td>
                                <td>
                                    <div class="quantity-control">
                                        <button class="quantity-btn" 
                                                onclick="updateQuantity(<?= $product_id ?>, <?= $item['quantity'] - 1 ?>)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" 
                                               id="quantity-<?= $product_id ?>" 
                                               value="<?= $item['quantity'] ?>" 
                                               min="1" max="10"
                                               class="quantity-input"
                                               onchange="updateQuantity(<?= $product_id ?>, this.value)">
                                        <button class="quantity-btn" 
                                                onclick="updateQuantity(<?= $product_id ?>, <?= $item['quantity'] + 1 ?>)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark" id="total-<?= $product_id ?>">
                                        Rs. <?= number_format($item['item_total'], 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <button class="btn action-btn move-to-wishlist mb-1" 
                                                onclick="moveToWishlist(<?= $product_id ?>)">
                                            <i class="fas fa-heart me-1"></i> Save
                                        </button>
                                        <button class="btn action-btn remove-item" 
                                                onclick="removeFromCart(<?= $product_id ?>)">
                                            <i class="fas fa-trash me-1"></i> Remove
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="products.php" class="btn btn-outline-dark">
                        <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                    </a>
                    <a href="wishlist.php" class="btn btn-outline-primary">
                        <i class="fas fa-heart me-2"></i> View Wishlist
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="cart-summary">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    
                    <div class="summary-row">
                        <span>Subtotal (<?= $total_items ?> items)</span>
                        <span>Rs. <?= number_format($subtotal, 2) ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span class="<?= $shipping == 0 ? 'text-success' : '' ?>">
                            <?= $shipping == 0 ? 'FREE' : 'Rs. ' . number_format($shipping, 2) ?>
                        </span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Tax (5%)</span>
                        <span>Rs. <?= number_format($tax, 2) ?></span>
                    </div>
                    
                    <hr class="my-3">
                    
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span id="grand-total">Rs. <?= number_format($grand_total, 2) ?></span>
                    </div>
                    
                    <div class="mt-4">
                        <button class="btn btn-checkout" onclick="proceedToCheckout()">
                            <i class="fas fa-lock me-2"></i> PROCEED TO CHECKOUT
                        </button>
                    </div>
                    
                    <div class="mt-3">
                        <p class="small text-muted">
                            <i class="fas fa-shield-alt me-1"></i> Secure checkout · 256-bit SSL encryption
                        </p>
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3">Accepted Payment Methods</h6>
                        <div class="d-flex gap-2">
                            <i class="fab fa-cc-visa fa-2x text-primary"></i>
                            <i class="fab fa-cc-mastercard fa-2x text-danger"></i>
                            <i class="fab fa-cc-amex fa-2x text-info"></i>
                            <i class="fab fa-cc-paypal fa-2x text-primary"></i>
                        </div>
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

    // Update quantity
    async function updateQuantity(productId, newQuantity) {
        newQuantity = parseInt(newQuantity);
        
        if (newQuantity < 1) newQuantity = 1;
        if (newQuantity > 10) newQuantity = 10;
        
        try {
            const response = await fetch('cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_quantity&product_id=${productId}&quantity=${newQuantity}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update quantity input
                const quantityInput = document.getElementById(`quantity-${productId}`);
                if (quantityInput) {
                    quantityInput.value = newQuantity;
                }
                
                // Update cart badge
                const cartBadges = document.querySelectorAll('.cart-count');
                cartBadges.forEach(badge => {
                    badge.textContent = data.cart_count;
                });
                
                // Recalculate total for this row
                const priceElement = document.querySelector(`#cart-row-${productId} .price-tag`);
                if (priceElement) {
                    const priceText = priceElement.textContent.replace('Rs. ', '').replace(',', '');
                    const price = parseFloat(priceText);
                    const total = price * newQuantity;
                    
                    const totalElement = document.getElementById(`total-${productId}`);
                    if (totalElement) {
                        totalElement.textContent = 'Rs. ' + total.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
                
                // Recalculate grand total
                recalculateGrandTotal();
                showToast('Quantity updated', 'success');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error updating quantity', 'danger');
        }
    }

    // Remove from cart
    async function removeFromCart(productId) {
        if (!confirm('Remove this item from your cart?')) return;
        
        try {
            const response = await fetch('cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove_item&product_id=${productId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Remove row from table
                const row = document.getElementById(`cart-row-${productId}`);
                if (row) {
                    row.style.opacity = '0.5';
                    row.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        row.remove();
                        // Check if cart is empty
                        if (document.querySelectorAll('#cartTableBody tr').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
                
                // Update cart badge
                const cartBadges = document.querySelectorAll('.cart-count');
                cartBadges.forEach(badge => {
                    badge.textContent = data.cart_count;
                });
                
                // Recalculate grand total
                recalculateGrandTotal();
                showToast('Item removed from cart', 'success');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error removing item', 'danger');
        }
    }

    // Move to wishlist
    async function moveToWishlist(productId) {
        try {
            const response = await fetch('cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=move_to_wishlist&product_id=${productId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Remove row from table
                const row = document.getElementById(`cart-row-${productId}`);
                if (row) {
                    row.style.opacity = '0.5';
                    row.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        row.remove();
                        // Check if cart is empty
                        if (document.querySelectorAll('#cartTableBody tr').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
                
                // Update badges
                const cartBadges = document.querySelectorAll('.cart-count');
                cartBadges.forEach(badge => {
                    badge.textContent = data.cart_count;
                });
                
                const wishlistBadges = document.querySelectorAll('.wishlist-count');
                wishlistBadges.forEach(badge => {
                    badge.textContent = data.wishlist_count;
                    badge.style.display = 'inline-block';
                });
                
                showToast('Item moved to wishlist', 'success');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error moving to wishlist', 'danger');
        }
    }

    // Recalculate grand total
    function recalculateGrandTotal() {
        let subtotal = 0;
        let itemCount = 0;
        
        // Calculate from visible rows
        document.querySelectorAll('#cartTableBody tr').forEach(row => {
            const totalElement = row.querySelector('[id^="total-"]');
            const quantityInput = row.querySelector('input[type="number"]');
            
            if (totalElement && quantityInput) {
                const totalText = totalElement.textContent.replace('Rs. ', '').replace(/,/g, '');
                const quantity = parseInt(quantityInput.value);
                
                subtotal += parseFloat(totalText);
                itemCount += quantity;
            }
        });
        
        // Update summary (simplified - you might want to recalculate shipping and tax too)
        const shipping = subtotal > 5000 ? 0 : 500;
        const tax = subtotal * 0.05;
        const grandTotal = subtotal + shipping + tax;
        
        // Update summary display (if you have elements for these)
        const subtotalElement = document.querySelector('.summary-row:nth-child(1) span:nth-child(2)');
        if (subtotalElement) {
            subtotalElement.textContent = 'Rs. ' + subtotal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        
        const grandTotalElement = document.getElementById('grand-total');
        if (grandTotalElement) {
            grandTotalElement.textContent = 'Rs. ' + grandTotal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    // Proceed to checkout
    function proceedToCheckout() {
        if (confirm('Proceed to checkout?')) {
            window.location.href = 'checkout.php';
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Any initialization code
    });
    </script>
</body>
</html>