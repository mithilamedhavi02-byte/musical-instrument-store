<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;
$message = []; 

// ADD TO CART LOGIC
if(isset($_POST['add_to_cart'])){
   if($user_id == 0){
      header('location:login.php'); 
      exit();
   }

// ... කලින් තිබුණු කේතය ...
   $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $p_price = mysqli_real_escape_string($conn, $_POST['product_price']);
   $p_image = mysqli_real_escape_string($conn, $_POST['product_image']);
   $p_qty = mysqli_real_escape_string($conn, $_POST['product_quantity']);
   
   // ERROR FIX: isset එකක් දාන්න එතකොට Warning එක එන්නේ නැහැ
   $p_pdf = isset($_POST['product_pdf']) ? mysqli_real_escape_string($conn, $_POST['product_pdf']) : ''; 

   $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$p_name' AND user_id = '$user_id'") or die('query failed');
// ... ඉතිරි කේතය ...

   if(mysqli_num_rows($check_cart) > 0){
      $message[] = 'Instrument already in your cart!';
   } else {
      // ඔබේ cart table එකේ pdf_url column එක තිබිය යුතුයි
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image, pdf_url) VALUES('$user_id', '$p_name', '$p_price', '$p_qty', '$p_image', '$p_pdf')") or die('query failed');
      $message[] = 'Added to cart successfully!';
   }
}

// ==========================================
// 2. ADD TO WISHLIST LOGIC (Products.php)
// ==========================================
if(isset($_POST['add_to_wishlist'])){
   if($user_id == 0){
      header('location:login.php');
      exit();
   }

   $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $p_price = mysqli_real_escape_string($conn, $_POST['product_price']);
   
   // --- MEKA BALANNA ---
   // Oya image eka pass karaddi input field eke dapu nama methana thiyenna ona
   $p_image = mysqli_real_escape_string($conn, $_POST['product_image']); 

   $check_wishlist = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '$p_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($check_wishlist) > 0){
      $message[] = 'Instrument already in your wishlist!';
   } else {
      // Database eke table eke column name eka 'image' nam mehema danna
      mysqli_query($conn, "INSERT INTO `wishlist`(user_id, name, price, image) VALUES('$user_id', '$p_name', '$p_price', '$p_image')") or die('query failed');
      $message[] = 'Added to wishlist!';
   }
}
$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
?>

<?php include 'header.php'; ?>

<style>
    .products-hero {
        background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), 
                    url('https://images.unsplash.com/photo-1514119412350-e174d90d280e?auto=format&fit=crop&w=1600&q=80');
        background-size: cover; background-position: center; background-attachment: fixed;
        padding: 80px 0; position: relative;
    }
    
    .product-card {
        background: white; border-radius: 20px; transition: all 0.4s ease;
        position: relative; overflow: hidden; height: 100%; border: 1px solid #eee !important;
        display: flex; flex-direction: column;
    }
    
    .product-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
    
    .product-image { 
        position: relative; overflow: hidden; border-radius: 15px; 
        background: #fdfdfd; display: flex; align-items: center; justify-content: center;
    }
    .product-image img { transition: transform 0.5s ease; width: 100%; height: 200px; object-fit: cover; }
    
    /* Digital Image Specific Styling */
    .product-card[data-type="digital"] .product-image { background: #fff9e6; padding: 20px; }
    .product-card[data-type="digital"] .product-image img { object-fit: contain; transform: none !important; }

    .product-title { font-weight: 700; color: #222; margin-top: 10px; font-size: 1.1rem; }
    
    .product-description {
        font-size: 0.85rem; color: #666; margin-bottom: 12px;
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;
        overflow: hidden; min-height: 50px; line-height: 1.3;
    }

    .product-price { color: #ffc107; font-size: 1.2rem; font-weight: 800; margin-bottom: 15px; }
    
    .btn-add-to-cart {
        background: linear-gradient(45deg, #ffc107, #ff9800); border: none;
        color: #000; font-weight: 700; border-radius: 10px; padding: 10px;
    }
    .btn-wishlist { border: 2px solid #ffc107; color: #ffc107; border-radius: 10px; width: 45px; transition: 0.3s; }
    .btn-wishlist:hover { background: #ffc107; color: #000; }

    .category-filter { background: #1a1a1a; border-bottom: 3px solid #ffc107; }
    .custom-alert { background: #ffc107; color: #000; font-weight: 600; border-radius: 10px; }
</style>

<div class="products-hero text-center text-white">
    <div class="container">
        <h1 class="fw-bold text-warning display-4">Melody Masters Shop</h1>
        <p class="lead">Premium gear & digital resources for your musical journey</p>
    </div>
</div>

<div class="category-filter sticky-top py-3" style="top: 70px; z-index: 100;">
   <div class="container text-center">
      <div class="d-flex flex-wrap justify-content-center gap-2">
         <a href="products.php" class="btn btn-outline-warning btn-sm rounded-pill <?php echo ($category_filter == '') ? 'active' : ''; ?>">All Products</a>
         <?php
            $get_nav_cats = mysqli_query($conn, "SELECT DISTINCT category FROM `products` WHERE category != ''") or die('query failed');
            while($nav_row = mysqli_fetch_assoc($get_nav_cats)){
               $cat = $nav_row['category'];
               $active = ($category_filter == $cat) ? 'active' : '';
               echo "<a href='products.php?category=$cat' class='btn btn-outline-warning btn-sm rounded-pill $active'>$cat</a>";
            }
         ?>
      </div>
   </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <?php
            $query = "SELECT * FROM `products`";
            if($category_filter != '') { $query .= " WHERE category = '$category_filter'"; }
            $select_products = mysqli_query($conn, $query) or die('Query failed');
            
            if(mysqli_num_rows($select_products) > 0){
                while($fetch_products = mysqli_fetch_assoc($select_products)){
                    // Image Logic for Digital vs Physical
                    if($fetch_products['type'] == 'digital'){
                        $display_img = 'https://cdn-icons-png.flaticon.com/512/3063/3063661.png'; // Sheet Music Icon
                    } else {
                        $img_path = "uploaded_img/" . $fetch_products['image_url'];
                        $display_img = (!empty($fetch_products['image_url']) && file_exists($img_path)) ? $img_path : 'uploaded_img/default.jpg';
                    }
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <form action="" method="post" class="product-card p-3 shadow-sm" data-type="<?php echo $fetch_products['type']; ?>">
                <div class="product-image">
                    <img src="<?php echo $display_img; ?>" alt="Product">
                    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">
                        <?php echo $fetch_products['category']; ?>
                    </span>
                   
                </div>
                
                <div class="card-body text-center px-0 pb-0">
                    <h6 class="product-title text-truncate"><?php echo $fetch_products['name']; ?></h6>
                    
                    <p class="product-description">
                        <?php echo (!empty($fetch_products['description'])) ? $fetch_products['description'] : 'Premium quality musical resource from Melody Masters.'; ?>
                    </p>

                    <div class="product-price">Rs. <?php echo number_format($fetch_products['price'], 2); ?></div>
                    
                    <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo ($fetch_products['type'] == 'digital') ? 'digital_icon.png' : $fetch_products['image_url']; ?>">
                    
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text bg-light border-0">Qty</span>
                        <input type="number" name="product_quantity" value="1" min="1" class="form-control border-0 text-center bg-light">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" name="add_to_cart" class="btn btn-add-to-cart flex-grow-1">
                            <i class="fas fa-shopping-cart me-1"></i> Add
                        </button>
                        <button type="submit" name="add_to_wishlist" class="btn btn-wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
                }
            } else {
                echo '<div class="col-12 text-center py-5"><p class="lead">No instruments or sheet music found in this category.</p></div>';
            }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>