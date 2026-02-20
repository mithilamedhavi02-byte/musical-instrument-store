<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user_id = $_SESSION['user_id'] ?? 0;

if($user_id == 0){ header('location:login.php'); exit(); }

$cart_total = 0;
$shipping_cost = 0;

// Cart total eka calculate karagannawa UI eke pennanna
$check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
while($row = mysqli_fetch_assoc($check_cart)){
    $cart_total += ($row['price'] * $row['quantity']);
}
$shipping_cost = ($cart_total > 100) ? 0.00 : 10.00;

if(isset($_POST['place_order'])){
   $cart_query = mysqli_query($conn, "SELECT cart.*, products.id as pid, products.stock_quantity FROM `cart` JOIN `products` ON cart.name = products.name WHERE cart.user_id = '$user_id'");
   
   if(mysqli_num_rows($cart_query) > 0){
      while($item = mysqli_fetch_assoc($cart_query)){
         // INVENTORY CONTROL: Stock adu kireema
         $new_stock = $item['stock_quantity'] - $item['quantity'];
         mysqli_query($conn, "UPDATE `products` SET stock_quantity = '$new_stock' WHERE id = '".$item['pid']."'");
      }

      $method = mysqli_real_escape_string($conn, $_POST['payment_method']);
      $order_date = date('Y-m-d H:i:s');

      // DATABASE FIX: Oyage orders table eke thiyena columns walata witharak data damma
      $insert_order = mysqli_query($conn, "INSERT INTO `orders`(user_id, total_amount, shipping_cost, payment_method, status, order_date) 
          VALUES('$user_id', '$cart_total', '$shipping_cost', '$method', 'Completed', '$order_date')");

      if($insert_order){
          mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'");
          echo "<script>alert('Order placed successfully!'); window.location.href='products.php';</script>";
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<?php include 'header.php'; ?>

<div class="container py-5">
   <div class="row g-5">
      <div class="col-md-5 order-md-last">
         <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-primary fw-bold">Order Summary</span>
         </h4>
         <ul class="list-group mb-3 shadow-sm">
            <?php
            $cart_summary = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
            while($item = mysqli_fetch_assoc($cart_summary)){
            ?>
            <li class="list-group-item d-flex justify-content-between lh-sm">
               <div>
                  <h6 class="my-0"><?php echo $item['name']; ?></h6>
                  <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
               </div>
               <span class="text-muted">£<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </li>
            <?php } ?>
            <li class="list-group-item d-flex justify-content-between bg-light">
               <span class="text-success">Shipping (Over £100 FREE)</span>
               <strong class="text-success"><?php echo ($shipping_cost == 0) ? 'FREE' : '£'.number_format($shipping_cost, 2); ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
               <span>Total (GBP)</span>
               <strong class="fs-5">£<?php echo number_format($cart_total + $shipping_cost, 2); ?></strong>
            </li>
         </ul>
      </div>

      <div class="col-md-7">
         <div class="card p-4 shadow-sm border-0">
            <h4 class="mb-4 fw-bold">Complete Your Purchase</h4>
            <form action="" method="POST">
               <div class="mb-3">
                  <label class="form-label">Delivery Address</label>
                  <textarea name="address" class="form-control" rows="3" placeholder="Enter your full address" required></textarea>
               </div>

               <div class="mb-3">
                  <label class="form-label">Phone Number</label>
                  <input type="text" name="phone" class="form-control" placeholder="07XXXXXXXX" required>
               </div>

               <hr class="my-4">

               <h5 class="mb-3">Payment Method</h5>
               <div class="my-3">
                  <div class="form-check">
                     <input id="cod" name="payment_method" type="radio" value="Cash on Delivery" class="form-check-input" checked required>
                     <label class="form-check-label" for="cod">Cash on Delivery</label>
                  </div>
                  <div class="form-check">
                     <input id="card" name="payment_method" type="radio" value="Credit/Debit Card" class="form-check-input" required>
                     <label class="form-check-label" for="card">Credit or Debit Card</label>
                  </div>
               </div>

               <button class="w-100 btn btn-warning btn-lg fw-bold mt-3" type="submit" name="place_order">PLACE ORDER NOW</button>
            </form>
         </div>
      </div>
   </div>
</div>

</body>
</html>