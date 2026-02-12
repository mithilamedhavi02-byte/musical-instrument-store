<?php
include 'config.php';
session_start();

// Meka thamai oyawa Login ekata yawapu "Redda"
// Me kalla temporary ain karala balanna page eka wada karanawada kiyala
// if(!isset($_SESSION['admin_id'])){ header('location:login.php'); exit(); }

if(isset($_POST['add_product'])){
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $price = $_POST['price'];
   $qty = $_POST['stock'];
   $type = $_POST['type']; 
   $image = $_FILES['image']['name'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploads/'.$image;

   // Database table columns anuwa update kireema
   $add_query = "INSERT INTO `products`(name, price, stock, type, image_url) 
                 VALUES('$name', '$price', '$qty', '$type', '$image')";
   
   $insert_product = mysqli_query($conn, $add_query);

   if($insert_product){
      move_uploaded_file($image_tmp_name, $image_folder);
      echo "<script>alert('Product added successfully!');</script>";
   }else{
      echo "<script>alert('Product could not be added!');</script>";
   }
}
?>

<?php include 'header.php'; ?>

<div class="container py-5 mt-5">
   <div class="row justify-content-center">
      <div class="col-md-6 card p-4 shadow-sm">
         <h3 class="mb-4 text-center">Add New Instrument</h3>
         <form action="" method="post" enctype="multipart/form-data">
            <input type="text" name="name" class="form-control mb-2" placeholder="Product Name" required>
            <input type="number" step="0.01" name="price" class="form-control mb-2" placeholder="Price (£)" required>
            <input type="number" name="stock" class="form-control mb-2" placeholder="Stock Quantity" required>
            <select name="type" class="form-control mb-2">
                <option value="physical">Physical Instrument</option>
                <option value="digital">Digital Sheet Music</option>
            </select>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="form-control mb-3" required>
            <input type="submit" value="Add Product" name="add_product" class="btn btn-warning w-100 fw-bold">
         </form>
      </div>
   </div>
</div>