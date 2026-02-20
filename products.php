<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;
$message = []; // Alerts pennanna array ekak

// ==========================================
// 1. ADD TO CART LOGIC
// ==========================================
if(isset($_POST['add_to_cart'])){
   if($user_id == 0){
      header('location:login.php'); 
      exit();
   }

   $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $p_price = mysqli_real_escape_string($conn, $_POST['product_price']);
   $p_image = mysqli_real_escape_string($conn, $_POST['product_image']);
   $p_qty = mysqli_real_escape_string($conn, $_POST['product_quantity']);

   // Check if already in cart
   $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$p_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($check_cart) > 0){
      $message[] = 'Instrument already in your cart!';
   } else {
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$p_name', '$p_price', '$p_qty', '$p_image')") or die('query failed');
      $message[] = 'Added to cart successfully!';
   }
}

// ==========================================
// 2. ADD TO WISHLIST LOGIC
// ==========================================
if(isset($_POST['add_to_wishlist'])){
   if($user_id == 0){
      header('location:login.php');
      exit();
   }

   $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $p_price = mysqli_real_escape_string($conn, $_POST['product_price']);
   $p_image = mysqli_real_escape_string($conn, $_POST['product_image']);

   // Check if already in wishlist
   $check_wishlist = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '$p_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($check_wishlist) > 0){
      $message[] = 'Instrument already in your wishlist!';
   } else {
      mysqli_query($conn, "INSERT INTO `wishlist`(user_id, name, price, image) VALUES('$user_id', '$p_name', '$p_price', '$p_image')") or die('query failed');
      $message[] = 'Added to wishlist!';
   }
}

$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
?>

<?php include 'header.php'; ?>

<style>
    /* Products page specific styles */
    .products-hero {
        background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), 
                    url('https://images.unsplash.com/photo-1514119412350-e174d90d280e?auto=format&fit=crop&w=1600&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }
    
    .products-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M20 50 L30 40 L40 50 L50 30 L60 50 L70 35 L80 50" stroke="%23ffc107" fill="none" stroke-width="2"/><circle cx="25" cy="60" r="3" fill="%23ffc107"/><circle cx="45" cy="60" r="3" fill="%23ffc107"/><circle cx="65" cy="60" r="3" fill="%23ffc107"/></svg>');
        background-size: 200px 200px;
        animation: floatNotes 20s linear infinite;
    }
    
    @keyframes floatNotes {
        from { transform: translateY(0) rotate(0deg); }
        to { transform: translateY(-100%) rotate(10deg); }
    }
    
    .products-hero h1 {
        font-size: 4.5rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 3px;
        animation: fadeInScale 1s ease-out;
    }
    
    .products-hero p {
        font-size: 1.3rem;
        animation: fadeInUp 1s ease-out 0.3s backwards;
    }
    
    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.8);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Category filter bar */
    .category-filter {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        border-bottom: 2px solid #ffc107;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        backdrop-filter: blur(10px);
    }
    
    .category-filter .btn {
        transition: all 0.3s ease;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
        padding: 0.5rem 1.2rem;
    }
    
    .category-filter .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
    }
    
    .category-filter .btn.active {
        background-color: #ffc107 !important;
        color: #000 !important;
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.6);
    }
    
    /* Product cards */
    .product-card {
        background: white;
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    
    .product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ffc107, #ff9800, #ffc107);
        transform: translateX(-100%);
        transition: transform 0.5s ease;
    }
    
    .product-card:hover::before {
        transform: translateX(0);
    }
    
    .product-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 30px rgba(0,0,0,0.2) !important;
    }
    
    .product-image {
        position: relative;
        overflow: hidden;
        border-radius: 15px;
    }
    
    .product-image img {
        transition: transform 0.5s ease;
    }
    
    .product-card:hover .product-image img {
        transform: scale(1.1);
    }
    
    .category-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 2;
        animation: slideInLeft 0.5s ease;
    }
    
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .product-title {
        font-weight: 700;
        color: #333;
        transition: color 0.3s ease;
        font-size: 1rem;
    }
    
    .product-card:hover .product-title {
        color: #ffc107;
    }
    
    .product-price {
        color: #ffc107;
        font-size: 1.3rem;
        font-weight: 800;
        margin-bottom: 15px;
        position: relative;
        display: inline-block;
    }
    
    .product-price::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 25%;
        width: 50%;
        height: 2px;
        background: linear-gradient(90deg, transparent, #ffc107, transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { opacity: 0.3; }
        50% { opacity: 1; }
        100% { opacity: 0.3; }
    }
    
    /* Quantity input */
    .qty-input-group {
        background: #f8f9fa;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .qty-input-group .input-group-text {
        background: transparent;
        border: none;
        font-weight: 600;
        color: #ffc107;
    }
    
    .qty-input-group input {
        background: transparent;
        border: none;
        font-weight: 600;
    }
    
    .qty-input-group input:focus {
        box-shadow: none;
        background: #fff;
    }
    
    /* Action buttons */
    .btn-add-to-cart {
        background: linear-gradient(45deg, #ffc107, #ff9800);
        border: none;
        color: #000;
        font-weight: 700;
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
    }
    
    .btn-add-to-cart::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-add-to-cart:hover::before {
        left: 100%;
    }
    
    .btn-add-to-cart:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 20px rgba(255, 193, 7, 0.6);
    }
    
    .btn-wishlist {
        background: transparent;
        border: 2px solid #ffc107;
        color: #ffc107;
        transition: all 0.3s ease;
        border-radius: 10px;
        width: 45px;
    }
    
    .btn-wishlist:hover {
        background: #ffc107;
        color: #000;
        transform: rotate(360deg);
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.6);
    }
    
    /* Alert messages */
    .custom-alert {
        background: linear-gradient(45deg, #ffc107, #ff9800);
        border: none;
        color: #000;
        border-radius: 50px;
        padding: 1rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 5px 20px rgba(255, 193, 7, 0.4);
        animation: slideInDown 0.5s ease;
    }
    
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Empty state */
    .empty-state {
        padding: 80px 20px;
        animation: fadeIn 1s ease;
    }
    
    .empty-state i {
        font-size: 5rem;
        color: #ffc107;
        margin-bottom: 20px;
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-30px); }
        60% { transform: translateY(-15px); }
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .products-hero h1 {
            font-size: 2.5rem;
        }
        .products-hero p {
            font-size: 1rem;
        }
        .category-filter .btn {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
        }
        .product-card:hover {
            transform: translateY(-5px) scale(1.01);
        }
        .btn-wishlist:hover {
            transform: rotate(360deg) scale(1.1);
        }
    }
    
    @media (max-width: 576px) {
        .products-hero {
            padding: 60px 0;
        }
        .products-hero h1 {
            font-size: 2rem;
        }
        .product-title {
            font-size: 0.9rem;
        }
        .product-price {
            font-size: 1.1rem;
        }
        .btn-add-to-cart {
            font-size: 0.9rem;
        }
    }
</style>

<?php if(!empty($message)): ?>
    <div class="container mt-3">
        <?php foreach($message as $msg): ?>
            <div class="alert custom-alert alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-music me-2"></i> <?php echo $msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Hero Section -->
<div class="products-hero text-center text-white">
    <div class="container">
        <h1 class="fw-bold text-warning display-4">Our Instruments</h1>
        <p class="lead">Premium gear for your musical journey</p>
    </div>
</div>

<!-- Category Filter Bar -->
<div class="category-filter sticky-top py-3" style="top: 70px; z-index: 100;">
   <div class="container text-center">
      <div class="d-flex flex-wrap justify-content-center gap-2">
         <a href="products.php" class="btn btn-outline-warning btn-sm rounded-pill <?php echo ($category_filter == '') ? 'active' : ''; ?>">
            <i class="fas fa-music me-1"></i> All Instruments
         </a>
         
         <?php
            $get_nav_cats = mysqli_query($conn, "SELECT DISTINCT category FROM `products` WHERE category != ''") or die('query failed');
            while($nav_row = mysqli_fetch_assoc($get_nav_cats)){
               $cat_name = $nav_row['category'];
               $active_class = ($category_filter == $cat_name) ? 'active' : '';
               echo "<a href='products.php?category=$cat_name' class='btn btn-outline-warning btn-sm rounded-pill $active_class'><i class='fas fa-tag me-1'></i>$cat_name</a>";
            }
         ?>
      </div>
   </div>
</div>

<!-- Products Grid -->
<div class="container py-5">
    <h3 class="fw-bold mb-4 text-center text-uppercase tracking-wider" data-aos="fade-down">
        <?php echo ($category_filter != '') ? '<i class="fas fa-filter me-2"></i> ' . $category_filter : '<i class="fas fa-store me-2"></i> Explore All Products'; ?>
    </h3>

    <div class="row g-4">
        <?php
            $query = "SELECT * FROM `products`";
            if($category_filter != '') {
                $query .= " WHERE category = '$category_filter'";
            }
            
            $select_products = mysqli_query($conn, $query) or die('Query failed');
            
            // ERROR FIX: Variable eka initialize kala
            $index = 0; 

            if(mysqli_num_rows($select_products) > 0){
                while($fetch_products = mysqli_fetch_assoc($select_products)){
                    $img_path = "uploaded_img/" . $fetch_products['image_url'];
                    $display_img = (!empty($fetch_products['image_url']) && file_exists($img_path)) ? $img_path : 'https://via.placeholder.com/300x200?text=Melody+Masters';
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="<?php echo $index * 100; ?>">
            <form action="" method="post" class="product-card card h-100 shadow-sm border-0 p-3 rounded-4">
                <div class="product-image">
                    <img src="<?php echo $display_img; ?>" class="card-img-top rounded-3" style="height:200px; object-fit:cover;">
                    <span class="badge bg-dark category-badge opacity-75 small">
                        <i class="fas fa-tag me-1"></i> <?php echo $fetch_products['category']; ?>
                    </span>
                </div>
                
                <div class="card-body text-center px-0 pb-0">
                    <h6 class="product-title fw-bold text-dark mb-1 text-truncate"><?php echo $fetch_products['name']; ?></h6>
                    <div class="product-price">Rs.<?php echo number_format($fetch_products['price'], 2); ?></div>
                    
                    <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $fetch_products['image_url']; ?>">
                    
                    <div class="qty-input-group input-group input-group-sm mb-3">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-sort-numeric-up"></i></span>
                        <input type="number" name="product_quantity" value="1" min="1" class="form-control border-0 text-center bg-light">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" name="add_to_cart" class="btn btn-add-to-cart flex-grow-1 fw-bold shadow-sm rounded-3">
                            <i class="fa-solid fa-cart-plus me-1"></i> Add
                        </button>
                        <button type="submit" name="add_to_wishlist" class="btn btn-wishlist shadow-sm rounded-3">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
                $index++; // Variable eka increment kala
                }
            } else {
                echo '<div class="col-12 text-center empty-state"><i class="fas fa-guitar"></i><p class="lead text-muted">No instruments found in this category yet.</p><a href="products.php" class="btn btn-warning mt-3"><i class="fas fa-arrow-left me-2"></i>View All Products</a></div>';
            }
        ?>
    </div>
</div>

<!-- Add AOS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        offset: 50
    });
    
    // Add index variable for delay
    var index = 0;
</script>

<style>
.tracking-wider { letter-spacing: 2px; }
</style>

<?php include 'footer.php'; ?>