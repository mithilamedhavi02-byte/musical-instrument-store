<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user_id = $_SESSION['user_id'] ?? 0;

if($user_id == 0){ header('location:login.php'); exit(); }

if(isset($_POST['submit_review'])){
   $pid = $_POST['product_id'];
   $rating = $_POST['rating'];
   $comment = mysqli_real_escape_string($conn, $_POST['comment']);

   // Database table eke columns: id, product_id, user_id, rating, comment, created_at
   $insert = mysqli_query($conn, "INSERT INTO `reviews` (product_id, user_id, rating, comment) VALUES ('$pid', '$user_id', '$rating', '$comment')");
   
   if($insert){
      echo "<script>alert('Review submitted! Thank you.'); window.location.href='account.php';</script>";
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Submit Review | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <style>
      .review-card { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
      .btn-warning { background-color: #ffc107; border: none; }
   </style>
</head>
<body class="bg-light">

<?php include 'header.php'; ?>

<div class="container py-5">
   <div class="row justify-content-center">
      <div class="col-md-6">
         <div class="card review-card p-4">
            <h3 class="fw-bold mb-4 text-center">How was your purchase?</h3>
            <form action="" method="POST">
               <div class="mb-3">
                  <label class="form-label fw-bold">Instrument / Product</label>
                  <select name="product_id" class="form-select" required>
                     <?php
                        $products = mysqli_query($conn, "SELECT id, name FROM `products` ");
                        while($p = mysqli_fetch_assoc($products)){
                           echo "<option value='".$p['id']."'>".$p['name']."</option>";
                        }
                     ?>
                  </select>
               </div>

               <div class="mb-3">
                  <label class="form-label fw-bold">Your Rating</label>
                  <select name="rating" class="form-select" required>
                     <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                     <option value="4">⭐⭐⭐⭐ (Good)</option>
                     <option value="3">⭐⭐⭐ (Average)</option>
                     <option value="2">⭐⭐ (Poor)</option>
                     <option value="1">⭐ (Very Bad)</option>
                  </select>
               </div>

               <div class="mb-4">
                  <label class="form-label fw-bold">Comment</label>
                  <textarea name="comment" class="form-control" rows="4" placeholder="Was the instrument in good condition?" required></textarea>
               </div>

               <button type="submit" name="submit_review" class="btn btn-warning w-100 fw-bold py-2 shadow-sm">POST REVIEW</button>
               <a href="account.php" class="btn btn-link w-100 mt-2 text-muted text-decoration-none">Go Back</a>
            </form>
         </div>
      </div>
   </div>
</div>

</body>
</html>