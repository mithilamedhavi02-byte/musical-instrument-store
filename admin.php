<?php
include_once 'config.php';

if(!isset($_SESSION['admin_id'])){ header('location:login.php'); exit(); }

if(isset($_POST['add_product'])){
   $name = clean($_POST['name']);
   $price = $_POST['price'];
   $qty = $_POST['stock'];
   $type = $_POST['type']; 
   $image = $_FILES['image']['name'];
   $image_tmp = $_FILES['image']['tmp_name'];

   $query = "INSERT INTO `products`(name, price, stock_quantity, type, image_url) VALUES('$name', '$price', '$qty', '$type', '$image')";
   
   if(mysqli_query($conn, $query)){
      move_uploaded_file($image_tmp, 'uploads/'.$image);
      echo "<script>alert('Product Added Successfully!'); window.location.href='products.php';</script>";
   }
}
?>