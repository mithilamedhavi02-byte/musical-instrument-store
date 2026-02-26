<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;

// Update Logic
if(isset($_POST['update_cart'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['cart_quantity'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$qty' WHERE id = '$cart_id'");
   $message[] = 'Cart updated successfully!';
}

// Delete Logic
if(isset($_GET['remove'])){
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'");
   header('location:cart.php');
   exit();
}
?>

<?php include 'header.php'; ?>

<div class="container py-5" style="min-height: 80vh;">
    <h2 class="fw-bold mb-4 text-dark"><i class="fas fa-shopping-basket me-2 text-warning"></i>Your Music Cart</h2>
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                <table class="table align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="ps-4 py-3">Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $grand_total = 0;
                        $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
                        
                        if(mysqli_num_rows($cart_query) > 0){
                            while($fetch_cart = mysqli_fetch_assoc($cart_query)){
                                $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
                                $grand_total += $sub_total;
                        ?>
                        <tr>
                            <td class="ps-4 py-4">
                                <span class="fw-bold text-dark"><?php echo $fetch_cart['name']; ?></span>
                            </td>
                            <td class="text-muted">Rs. <?php echo number_format($fetch_cart['price'], 2); ?></td>
                            <td>
                                <form action="" method="post" class="d-flex align-items-center">
                                    <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                                    <input type="number" name="cart_quantity" min="1" class="form-control form-control-sm me-2 text-center" style="width: 60px; border-radius: 8px;" value="<?php echo $fetch_cart['quantity']; ?>">
                                    <button type="submit" name="update_cart" class="btn btn-outline-warning btn-sm fw-bold" style="border-radius: 8px;">Update</button>
                                </form>
                            </td>
                            <td class="fw-bold text-dark">Rs. <?php echo number_format($sub_total, 2); ?></td>
                            <td class="text-center">
                                <a href="cart.php?remove=<?php echo $fetch_cart['id']; ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('Remove this item?');" style="border-radius: 50%;">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="5" class="text-center py-5 text-muted">Your cart is feeling light! Start adding some music.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <a href="products.php" class="btn btn-link text-dark fw-bold mt-3 text-decoration-none small">
                <i class="fas fa-arrow-left me-2"></i> Continue Shopping
            </a>
        </div>

        <div class="col-lg-4">
            <?php if($grand_total > 0): 
                // NEW LOGIC: Under 1000 = Rs. 300 shipping. Over 1000 = FREE.
                $shipping = ($grand_total < 1000) ? 300 : 0;
                $final_bill = $grand_total + $shipping;
            ?>
            <div class="card p-4 shadow-sm border-0" style="border-radius: 20px; background: #fff;">
                <h5 class="fw-bold mb-4">Summary</h5>
                
                <div class="d-flex justify-content-between mb-3 text-muted">
                    <span>Items Total</span>
                    <span>Rs. <?php echo number_format($grand_total, 2); ?></span>
                </div>
                
                <div class="d-flex justify-content-between mb-3">
                    <span>Shipping</span>
                    <?php if($shipping == 0): ?>
                        <span class="badge bg-success-subtle text-success px-3">FREE</span>
                    <?php else: ?>
                        <span class="text-dark">Rs. <?php echo number_format($shipping, 2); ?></span>
                    <?php endif; ?>
                </div>

                <?php if($shipping > 0): ?>
                    <div class="alert alert-warning py-2 mb-4 border-0" style="font-size: 0.8rem; border-radius: 10px;">
                        <i class="fas fa-truck me-2"></i> Add <strong>Rs. <?php echo number_format(1000 - $grand_total, 2); ?></strong> more for <strong>FREE Delivery!</strong>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success py-2 mb-4 border-0" style="font-size: 0.8rem; border-radius: 10px;">
                        <i class="fas fa-check-circle me-2"></i> Congrats! You qualify for <strong>FREE Shipping</strong>.
                    </div>
                <?php endif; ?>

                <hr class="my-4">

                <div class="d-flex justify-content-between mb-4 align-items-center">
                    <span class="h6 mb-0 fw-bold">Grand Total</span>
                    <span class="h4 mb-0 fw-bold text-primary">Rs. <?php echo number_format($final_bill, 2); ?></span>
                </div>

                <a href="checkout.php" class="btn btn-warning w-100 fw-bold py-3 shadow-sm border-0 rounded-pill" style="font-size: 1.1rem; transition: 0.3s;">
                    PROCEED TO CHECKOUT
                </a>
                
                <div class="text-center mt-3">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" height="20" class="me-2 grayscale opacity-50" alt="PayPal">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" height="15" class="grayscale opacity-50" alt="Visa">
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .grayscale { filter: grayscale(100%); }
    .btn-warning:hover { background: #e5ac00 !important; transform: translateY(-2px); }
    .table thead { border-radius: 15px 15px 0 0; }
</style>