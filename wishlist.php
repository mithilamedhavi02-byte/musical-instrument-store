<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;

// ADD TO CART LOGIC
if(isset($_POST['add_to_cart'])){
   if($user_id == 0){
      header('location:login.php'); 
      exit();
   }

   $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $p_price = mysqli_real_escape_string($conn, $_POST['product_price']);
   $p_image = mysqli_real_escape_string($conn, $_POST['product_image']);
   $p_qty = $_POST['product_quantity'];

   $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$p_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($check_cart) > 0){
      $message[] = 'Instrument already in your cart!';
   } else {
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$p_name', '$p_price', '$p_qty', '$p_image')") or die('query failed');
      $message[] = 'Added to cart successfully!';
   }
}

if(isset($_GET['remove'])){
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `wishlist` WHERE id = '$remove_id'");
   header('location:wishlist.php');
   exit();
}

if(isset($_GET['delete_all'])){
   mysqli_query($conn, "DELETE FROM `wishlist` WHERE user_id = '$user_id'");
   header('location:wishlist.php');
   exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>My Wishlist | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
   
   <style>
      :root {
         --gold: #D4AF37;
         --gold-dark: #B8860B;
         --dark: #1a1a1a;
      }

      body {
         font-family: 'Poppins', sans-serif;
         background-color: #fcfcfc;
      }

      /* Hero Section */
      .heading {
         min-height: 25rem;
         display: flex;
         flex-direction: column;
         align-items: center;
         justify-content: center;
         background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                     url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1600&q=80') no-repeat;
         background-size: cover;
         background-position: center;
         background-attachment: fixed;
      }

      .heading h3 {
         font-size: 4rem;
         color: var(--gold);
         font-weight: 700;
         text-transform: uppercase;
         letter-spacing: 2px;
         text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
      }

      .heading p { font-size: 1.2rem; color: #ddd; }
      .heading p a { color: var(--gold); transition: 0.3s; }
      .heading p a:hover { color: #fff; }

      /* Wishlist Cards */
      .wishlist-card {
         background: #fff;
         border-radius: 15px;
         border: none;
         overflow: hidden;
         transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
         box-shadow: 0 5px 15px rgba(0,0,0,0.05);
         position: relative;
      }

      .wishlist-card:hover {
         transform: translateY(-10px);
         box-shadow: 0 15px 30px rgba(0,0,0,0.1);
      }

      .wishlist-img-wrapper {
         height: 220px;
         padding: 20px;
         background: #fdfdfd;
         display: flex;
         align-items: center;
         justify-content: center;
         position: relative;
      }

      .wishlist-img-wrapper img {
         max-width: 100%;
         max-height: 100%;
         object-fit: contain;
         transition: 0.5s;
      }

      .wishlist-card:hover img { transform: scale(1.1); }

      .card-body { padding: 1.5rem; }
      .product-title { font-weight: 700; color: var(--dark); margin-bottom: 5px; }
      .product-price { color: var(--gold-dark); font-weight: 700; font-size: 1.25rem; }

      /* Buttons */
      .btn-move-cart {
         background: linear-gradient(135deg, var(--gold), var(--gold-dark));
         color: #fff;
         border: none;
         padding: 10px;
         font-weight: 600;
         border-radius: 10px;
         transition: 0.3s;
      }

      .btn-move-cart:hover {
         transform: scale(1.02);
         box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
         color: #fff;
      }

      .btn-remove {
         background: #fff;
         color: #dc3545;
         border: 1px solid #fee2e2;
         border-radius: 10px;
         font-size: 0.9rem;
         transition: 0.3s;
      }

      .btn-remove:hover {
         background: #fff5f5;
         color: #a71d2a;
      }

      .clear-all-btn {
         background: transparent;
         color: #dc3545;
         border: 2px solid #dc3545;
         font-weight: 600;
         border-radius: 30px;
         padding: 8px 25px;
         transition: 0.3s;
      }

      .clear-all-btn:hover {
         background: #dc3545;
         color: #fff;
      }

      /* Alerts */
      .alert {
         border-radius: 15px;
         border: none;
         box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
   <h3>Wishlist</h3>
   <p> <a href="home.php" class="text-decoration-none">Home</a> <i class="fas fa-chevron-right mx-2 small"></i> Wishlist </p>
</div>

<?php if(!empty($message)): ?>
    <div class="container mt-4">
        <?php foreach($message as $msg): ?>
            <div class="alert alert-dark alert-dismissible fade show text-white" role="alert" style="background: var(--dark);">
                <i class="fas fa-info-circle me-2 text-warning"></i> <?php echo $msg; ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="container py-5">
    
    <?php
       $wishlist_query = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE user_id = '$user_id'");
       if(mysqli_num_rows($wishlist_query) > 0){
    ?>
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h4 class="fw-bold m-0"><i class="fas fa-heart text-danger me-2"></i> Saved Items</h4>
        <a href="wishlist.php?delete_all" class="clear-all-btn text-decoration-none" onclick="return confirm('Clear your entire wishlist?');">
            <i class="fas fa-trash-alt me-2"></i>Clear List
        </a>
    </div>

    <div class="row g-4">
        <?php
            while($fetch_wishlist = mysqli_fetch_assoc($wishlist_query)){
                $image_name = $fetch_wishlist['image'];

                if (filter_var($image_name, FILTER_VALIDATE_URL)) {
                    $display_img = $image_name;
                } elseif (file_exists("uploaded_img/" . $image_name) && !empty($image_name)) {
                    $display_img = "uploaded_img/" . $image_name;
                } else {
                    $display_img = 'uploaded_img/default.jpg';
                }
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="wishlist-card">
                <div class="wishlist-img-wrapper">
                    <img src="<?php echo $display_img; ?>" alt="product">
                </div>
                <div class="card-body text-center">
                    <h6 class="product-title text-truncate"><?php echo $fetch_wishlist['name']; ?></h6>
                    <p class="product-price">Rs. <?php echo number_format($fetch_wishlist['price'], 2); ?></p>
                    
                    <form action="" method="post">
                        <input type="hidden" name="product_name" value="<?php echo $fetch_wishlist['name']; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $fetch_wishlist['price']; ?>">
                        <input type="hidden" name="product_image" value="<?php echo $fetch_wishlist['image']; ?>">
                        <input type="hidden" name="product_quantity" value="1">
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="add_to_cart" class="btn btn-move-cart">
                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                            </button>
                            <a href="wishlist.php?remove=<?php echo $fetch_wishlist['id']; ?>" class="btn btn-remove btn-sm py-2" onclick="return confirm('Remove this item?');">
                                <i class="fas fa-trash-alt"></i> Remove
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <?php
       } else {
          echo '<div class="text-center py-5">
                  <div class="mb-4">
                    <i class="fas fa-heart-circle-exclamation fa-6x" style="color: #eee;"></i>
                  </div>
                  <h2 class="fw-bold text-muted">Your wishlist is empty</h2>
                  <p class="text-secondary mb-4">Seems like you haven\'t added any instruments to your favorites yet.</p>
                  
                </div>';
       }
    ?>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>