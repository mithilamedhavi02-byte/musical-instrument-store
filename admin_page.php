<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Admin කෙනෙක්ද කියලා check කරන එක මෙතනට වැදගත්
// if(!isset($_SESSION['admin_id'])){ header('location:login.php'); }

if(isset($_POST['add_product'])){
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $price = $_POST['price'];
   $stock = $_POST['stock'];
   $type = $_POST['type']; // physical හෝ digital
   $category = $_POST['category'];

   // Image upload logic
   $image = $_FILES['image']['name'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $insert_query = mysqli_query($conn, "INSERT INTO `products`(name, price, stock_quantity, type, category, image) 
      VALUES('$name', '$price', '$stock', '$type', '$category', '$image')") or die('query failed');

   if($insert_query){
      move_uploaded_file($image_tmp_name, $image_folder);
      $message[] = 'Product added successfully!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Add Product | Admin Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
   <div class="row justify-content-center">
      <div class="col-md-6">
         <div class="card shadow-sm p-4 border-0">
            <h3 class="fw-bold mb-4 text-center">Add New Product</h3>
            
            <form action="" method="post" enctype="multipart/form-data">
               <div class="mb-3">
                  <label class="form-label fw-bold">Instrument Name</label>
                  <input type="text" name="name" class="form-control" placeholder="e.g. Yamaha Acoustic Guitar" required>
               </div>

               <div class="mb-3">
                  <label class="form-label fw-bold">Price (£)</label>
                  <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
               </div>

               <div class="mb-3">
                  <label class="form-label fw-bold">Initial Stock</label>
                  <input type="number" name="stock" class="form-control" placeholder="10" required>
               </div>

               <div class="mb-3">
                  <label class="form-label fw-bold">Product Type (Crucial for Business Rules)</label>
                  <select name="type" class="form-select" required>
                     <option value="physical">Physical (Requires Shipping)</option>
                     <option value="digital">Digital (Downloadable Sheet Music)</option>
                  </select>
                  <small class="text-muted">Digital products enable the download feature for customers.</small>
               </div>

               <div class="mb-3">
                  <label class="form-label fw-bold">Category</label>
                  <select name="category" class="form-select">
                     <option value="Guitars">Guitars</option>
                     <option value="Keyboards">Keyboards</option>
                     <option value="Drums">Drums</option>
                     <option value="Sheet Music">Sheet Music</option>
                  </select>
               </div>

               <div class="mb-3">
                  <label class="form-label fw-bold">Product Image</label>
                  <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="form-control" required>
               </div>

               <button type="submit" name="add_product" class="btn btn-dark w-100 fw-bold">ADD PRODUCT TO SHOP</button>
               <a href="admin_page.php" class="btn btn-link w-100 text-decoration-none mt-2">Back to Dashboard</a>
            </form>
         </div>
      </div>
   </div>
</div>

</body>
</html>