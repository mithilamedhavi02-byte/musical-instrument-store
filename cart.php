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

// Remove item from cart
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header("Location: cart.php");
    exit();
}

// Update quantities
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $product_id = intval($product_id);
        $quantity = intval($quantity);
        
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
    }
    header("Location: cart.php");
    exit();
}

// Get cart items with product details
$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '%d'));
    
    if (!empty($product_ids)) {
        $sql = "SELECT * FROM products WHERE product_id IN (" . implode(',', $product_ids) . ")";
        $result = query($sql);
        $products = $result ? fetch_all($result) : [];
        
        foreach ($products as $product) {
            $quantity = $_SESSION['cart'][$product['product_id']]['quantity'] ?? 1;
            $item_total = $product['price'] * $quantity;
            $subtotal += $item_total;
            
            $cart_items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'total' => $item_total
            ];
        }
    }
}

$shipping = $subtotal > 5000 ? 0 : 500;
$grand_total = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - The Music Shop</title>
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
        .cart-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .quantity-input {
            width: 70px;
            text-align: center;
        }
        .cart-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        .btn-checkout {
            background: #121212;
            color: white;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-checkout:hover {
            background: #d4af37;
            color: black;
        }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <div class="container py-5">
        <h1 class="fw-bold mb-4">Shopping Cart</h1>
        
        <?php if(empty($cart_items)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted mb-4">Add some products to your cart</p>
            <a href="products.php" class="btn btn-primary btn-lg">Continue Shopping</a>
        </div>
        <?php else: ?>
        <form method="POST">
            <div class="table-responsive">
                <table class="table cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cart_items as $item): 
                            $product = $item['product'];
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= htmlspecialchars($product['image_url'] ?: 'https://via.placeholder.com/100') ?>" 
                                         style="width: 80px; height: 80px; object-fit: cover;" 
                                         class="rounded me-3" alt="<?= htmlspecialchars($product['name']) ?>">
                                    <div>
                                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($product['name']) ?></h6>
                                        <small class="text-muted">Category: <?= $product['category_name'] ?? 'N/A' ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <h6 class="mb-0">Rs. <?= number_format($product['price'], 2) ?></h6>
                            </td>
                            <td class="align-middle">
                                <input type="number" name="quantity[<?= $product['product_id'] ?>]" 
                                       value="<?= $item['quantity'] ?>" min="1" max="10" 
                                       class="form-control quantity-input">
                            </td>
                            <td class="align-middle">
                                <h6 class="mb-0 fw-bold text-primary">Rs. <?= number_format($item['total'], 2) ?></h6>
                            </td>
                            <td class="align-middle">
                                <a href="cart.php?remove=<?= $product['product_id'] ?>" 
                                   class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="products.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                </a>
                <button type="submit" name="update_cart" class="btn btn-primary">
                    <i class="fas fa-sync me-2"></i> Update Cart
                </button>
            </div>
        </form>
        
        <div class="row mt-5">
            <div class="col-lg-5 offset-lg-7">
                <div class="cart-summary">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span class="fw-bold">Rs. <?= number_format($subtotal, 2) ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping:</span>
                        <span class="<?= $shipping == 0 ? 'text-success' : '' ?>">
                            <?= $shipping == 0 ? 'FREE' : 'Rs. ' . number_format($shipping, 2) ?>
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <h5 class="fw-bold">Total:</h5>
                        <h4 class="fw-bold text-primary">Rs. <?= number_format($grand_total, 2) ?></h4>
                    </div>
                    
                    <div class="d-grid">
                        <a href="checkout.php" class="btn btn-checkout">
                            <i class="fas fa-lock me-2"></i> PROCEED TO CHECKOUT
                        </a>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i> Secure checkout · 256-bit SSL encryption
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php include "includes/footer.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>