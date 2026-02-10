<?php
// checkout.php
session_start();
require_once "includes/db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = 'checkout.php';
    header("Location: login.php?redirect_from=checkout");
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Get user details
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$result = query($sql);
$user = $result ? fetch_one($result) : null;

if (!$user) {
    header("Location: logout.php");
    exit();
}

// Calculate cart total
$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
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

// Process checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = escape($_POST['shipping_address']);
    $payment_method = escape($_POST['payment_method']);
    $phone = escape($_POST['phone']);
    
    // Create order
    $order_sql = "INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, phone) 
                  VALUES ($user_id, $grand_total, '$shipping_address', '$payment_method', '$phone')";
    
    if (query($order_sql)) {
        $order_id = last_id();
        
        // Add order items
        foreach ($cart_items as $item) {
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                         VALUES ($order_id, {$item['product']['product_id']}, {$item['quantity']}, {$item['product']['price']})";
            query($item_sql);
        }
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        // Redirect to success page
        header("Location: order_success.php?id=$order_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - The Music Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding-top: 70px;
        }
        .checkout-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        .order-summary {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
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
        <h1 class="fw-bold mb-4">Checkout</h1>
        
        <form method="POST">
            <div class="row">
                <div class="col-lg-8">
                    <div class="checkout-card mb-4">
                        <h5 class="fw-bold mb-4">Shipping Information</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Shipping Address *</label>
                                <textarea name="shipping_address" class="form-control" rows="3" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="checkout-card">
                        <h5 class="fw-bold mb-4">Payment Method</h5>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       value="cash_on_delivery" id="cod" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave me-2"></i> Cash on Delivery
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       value="card" id="card">
                                <label class="form-check-label" for="card">
                                    <i class="fas fa-credit-card me-2"></i> Credit/Debit Card
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       value="bank_transfer" id="bank">
                                <label class="form-check-label" for="bank">
                                    <i class="fas fa-university me-2"></i> Bank Transfer
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="order-summary">
                        <h5 class="fw-bold mb-4">Order Summary</h5>
                        
                        <?php foreach($cart_items as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= htmlspecialchars($item['product']['name']) ?> × <?= $item['quantity'] ?></span>
                            <span>Rs. <?= number_format($item['total'], 2) ?></span>
                        </div>
                        <?php endforeach; ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>Rs. <?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping:</span>
                            <span class="<?= $shipping == 0 ? 'text-success fw-bold' : '' ?>">
                                <?= $shipping == 0 ? 'FREE' : 'Rs. ' . number_format($shipping, 2) ?>
                            </span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="fw-bold">Total:</h5>
                            <h5 class="fw-bold text-primary">Rs. <?= number_format($grand_total, 2) ?></h5>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-checkout">
                                <i class="fas fa-check-circle me-2"></i> PLACE ORDER
                            </button>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                By placing your order, you agree to our <a href="#">Terms & Conditions</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <?php include "includes/footer.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>