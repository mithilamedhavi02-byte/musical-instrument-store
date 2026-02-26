<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user_id = $_SESSION['user_id'] ?? 0;

if($user_id == 0){ header('location:login.php'); exit(); }

$cart_total = 0;
$shipping_cost = 0;

// Calculate Cart Total
$check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
while($row = mysqli_fetch_assoc($check_cart)){
    $cart_total += ($row['price'] * $row['quantity']);
}
$shipping_cost = ($cart_total > 100) ? 0.00 : 10.00;
$grand_total = $cart_total + $shipping_cost;

if(isset($_POST['place_order'])){

   $address = mysqli_real_escape_string($conn, $_POST['address']);
   $number = mysqli_real_escape_string($conn, $_POST['phone']); 
   $method = mysqli_real_escape_string($conn, $_POST['payment_method']);
   $status = ($method == 'Credit/Debit Card') ? 'delivered' : 'pending';
   $order_date = date('Y-m-d H:i:s');

   $insert_order = mysqli_query($conn, "INSERT INTO `orders`(user_id, address, number, method, total_amount, status, order_date, total_products) 
       VALUES('$user_id', '$address', '$number', '$method', '$grand_total', '$status', '$order_date', '')") or die(mysqli_error($conn));
   
   $order_id = mysqli_insert_id($conn);

   $cart_query = mysqli_query($conn, "SELECT cart.*, products.id as pid, products.stock_quantity, products.type FROM `cart` JOIN `products` ON cart.name = products.name WHERE cart.user_id = '$user_id'");

   $all_product_names = []; 

   if(mysqli_num_rows($cart_query) > 0){
      while($item = mysqli_fetch_assoc($cart_query)){
          $new_stock = $item['stock_quantity'] - $item['quantity'];
          mysqli_query($conn, "UPDATE `products` SET stock_quantity = '$new_stock' WHERE id = '".$item['pid']."'");

          $all_product_names[] = $item['name'];

          $product_name = $item['name'];
          $product_price = $item['price'];
          $product_qty = $item['quantity'];
          mysqli_query($conn, "INSERT INTO `order_items`(order_id, product_name, price, quantity) VALUES('$order_id', '$product_name', '$product_price', '$product_qty')");
      }
      $final_products_string = implode(', ', $all_product_names);
      mysqli_query($conn, "UPDATE `orders` SET total_products = '$final_products_string' WHERE id = '$order_id'");
   }
      
   mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'");

   echo "<script>
        alert('Order placed successfully! Access your digital sheets in your account.'); 
        window.location.href='account.php';
   </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <style>
       body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
       .card { border-radius: 15px; border: none; }
       .hint-text { font-size: 0.75rem; color: #888; margin-top: 5px; display: flex; align-items: center; }
       .hint-text i { margin-right: 5px; color: #f39c12; }
       .form-control { padding: 12px; border-radius: 10px; border: 1px solid #ddd; }
       .form-control:focus { border-color: #f39c12; box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.1); }
       .btn-confirm { background: #ffc107; color: white; padding: 15px; border-radius: 10px; font-weight: 600; width: 100%; transition: 0.3s; }
       .btn-confirm:hover { background: #ffc107; transform: translateY(-2px); }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container py-5">
   <div class="row g-5">
      <div class="col-md-5 order-md-last">
         <h4 class="mb-3 fw-bold">Order Summary</h4>
         <div class="card shadow-sm p-3">
            <ul class="list-group list-group-flush">
               <?php
               $cart_summary = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
               while($item = mysqli_fetch_assoc($cart_summary)){
               ?>
               <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                   <div>
                     <h6 class="mb-0 fw-bold"><?php echo $item['name']; ?></h6>
                     <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                   </div>
                   <span class="fw-bold">Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
               </li>
               <?php } ?>
               <hr>
               <li class="d-flex justify-content-between">
                   <span>Shipping</span>
                   <span class="text-success fw-bold"><?php echo ($shipping_cost == 0) ? 'FREE' : 'Rs. '.number_format($shipping_cost, 2); ?></span>
               </li>
               <li class="d-flex justify-content-between mt-2">
                   <h5 class="fw-bold">Total</h5>
                   <h5 class="fw-bold text-primary">Rs. <?php echo number_format($grand_total, 2); ?></h5>
               </li>
            </ul>
         </div>
      </div>

      <div class="col-md-7">
         <div class="card shadow-sm p-4">
            <h4 class="mb-4 fw-bold">Billing & Delivery</h4>
            <form action="" method="POST">
                
                <div class="mb-3">
                   <label class="form-label small fw-bold">Full Address</label>
                   <textarea name="address" class="form-control" rows="3" placeholder="No 123, Main Street, Colombo" required></textarea>
                   <div class="hint-text"><i class="fas fa-map-marker-alt"></i> Please provide your full home or office address for delivery.</div>
                </div>

                <div class="mb-3">
                   <label class="form-label small fw-bold">Phone Number</label>
                   <input type="text" name="phone" class="form-control" placeholder="07XXXXXXXX" maxlength="10" required 
                          oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                   <div class="hint-text"><i class="fas fa-phone"></i> Enter 10 digits only. No letters or spaces allowed.</div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3 fw-bold">Payment Method</h5>
                <div class="p-3 border rounded mb-4">
                   <div class="form-check mb-2">
                      <input id="cod" name="payment_method" type="radio" value="Cash on Delivery" class="form-check-input" checked required>
                      <label class="form-check-label" for="cod">Cash on Delivery (Pay when you receive)</label>
                   </div>
                   <div class="form-check">
                      <input id="card" name="payment_method" type="radio" value="Credit/Debit Card" class="form-check-input" required>
                      <label class="form-check-label" for="card">Credit or Debit Card (Instant Online Pay)</label>
                   </div>
                </div>

                <div id="card_section" style="display: none;" class="bg-light p-4 rounded border mb-4">
                   <h6 class="fw-bold mb-3"><i class="fas fa-lock text-success me-2"></i>Secure Card Payment</h6>
                   <div class="mb-3">
                       <label class="small fw-bold">Card Number</label>
                       <input type="text" id="card_no" class="form-control" placeholder="1234 5678 9101 1121" maxlength="16"
                              oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                       <div class="hint-text">The 16-digit number on the front of your card.</div>
                   </div>
                   <div class="row">
                       <div class="col-md-6 mb-3">
                           <label class="small fw-bold">Expiry Date</label>
                           <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                       </div>
                       <div class="col-md-6 mb-3">
                           <label class="small fw-bold">CVV</label>
                           <input type="password" class="form-control" placeholder="***" maxlength="3"
                                  oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                           <div class="hint-text">3-digit code on the back of your card.</div>
                       </div>
                   </div>
                </div>

                <button type="submit" name="place_order" class="btn-confirm border-0 shadow">PLACE ORDER NOW</button>
            </form>
         </div>
      </div>
   </div>
</div>

<script>
    const cardRadio = document.getElementById('card');
    const codRadio = document.getElementById('cod');
    const cardSection = document.getElementById('card_section');

    cardRadio.addEventListener('change', function() {
        if(this.checked) {
            cardSection.style.display = 'block';
            cardSection.querySelectorAll('input').forEach(i => i.required = true);
        }
    });

    codRadio.addEventListener('change', function() {
        if(this.checked) {
            cardSection.style.display = 'none';
            cardSection.querySelectorAll('input').forEach(i => i.required = false);
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>