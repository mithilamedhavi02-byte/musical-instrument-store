<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin'){
   header('location:login.php');
   exit();
}

// Product add logic
if(isset($_POST['add_product'])){
   $price = $_POST['price'];
   $qty = $_POST['stock'];
   $type = $_POST['product_type']; // Digital/Physical type eka ganeema
   $selected_val = mysqli_real_escape_string($conn, $_POST['product_name']);
   
   if($selected_val == 'add_new'){
      $name = mysqli_real_escape_string($conn, $_POST['new_item_name']);
      $category = $name; 
   } else {
      $name = $selected_val;
      $category = $selected_val;
   }

   $image = $_FILES['image']['name'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   if(!empty($name)){
      // UPDATED QUERY: 'type' column eka database ekata save kireema
      $add_query = mysqli_query($conn, "INSERT INTO `products`(name, price, stock_quantity, category, image_url, type) VALUES('$name', '$price', '$qty', '$category', '$image', '$type')") or die('query failed');
      
      if($add_query){
         if(!is_dir('uploaded_img')){ mkdir('uploaded_img'); }
         move_uploaded_file($image_tmp_name, $image_folder);
         echo "<script>alert('Product Added Successfully!');</script>";
      }
   }
}

// Delete logic
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `products` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_products.php');
   exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Product Management | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <style>
      .main-content { padding: 20px; background: #f8f9fa; min-height: 100vh; }
      .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
      .form-label { font-weight: 600; font-size: 0.85rem; color: #444; }
      .btn-warning { background: #ffc107; border: none; font-weight: bold; color: #000; }
      .type-badge { font-size: 0.7rem; padding: 3px 8px; border-radius: 10px; text-transform: uppercase; }
   </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<div class="main-content">
   <div class="container-fluid">
      <h3 class="fw-bold mb-4"><i class="fas fa-boxes me-2"></i>Inventory Management</h3>

      <div class="row g-4">
         <div class="col-md-4">
            <div class="card p-4">
               <h5 class="fw-bold mb-3 text-warning"><i class="fas fa-plus-circle me-2"></i>Product Entry</h5>
               <form action="" method="post" enctype="multipart/form-data">
                  
                  <div class="mb-3">
                     <label class="form-label">Instrument Name (Select or Add New)</label>
                     <select name="product_name" id="productDropdown" class="form-select" onchange="toggleNewInput(this.value)" required>
                        <option value="" disabled selected>Select Category / Instrument</option>
                        <?php
                           $get_cats = mysqli_query($conn, "SELECT DISTINCT category FROM `products` WHERE category != ''") or die('query failed');
                           while($c_row = mysqli_fetch_assoc($get_cats)){
                              echo "<option value='".$c_row['category']."'>".$c_row['category']."</option>";
                           }
                        ?>
                        <option value="add_new" style="background: #fff3cd; font-weight: bold;">+ Add New Product</option>
                     </select>
                  </div>

                  <div id="new_item_box" class="mb-3" style="display: none;">
                     <label class="form-label text-primary">Enter New Instrument Name</label>
                     <input type="text" name="new_item_name" class="form-control border-primary" placeholder="e.g. Electric Violin">
                  </div>

                  <div class="mb-3">
                     <label class="form-label">Product Distribution Type</label>
                     <select name="product_type" class="form-select" required>
                        <option value="physical">Physical Instrument (Requires Shipping)</option>
                        <option value="digital">Digital Sheet Music (Instant Download)</option>
                     </select>
                  </div>

                  <div class="row mb-3">
                     <div class="col">
                        <label class="form-label">Price (£)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                     </div>
                     <div class="col">
                        <label class="form-label">Stock Units</label>
                        <input type="number" name="stock" class="form-control" required>
                     </div>
                  </div>

                  <div class="mb-4">
                     <label class="form-label">Product Image</label>
                     <input type="file" name="image" class="form-control" required>
                  </div>

                  <button type="submit" name="add_product" class="btn btn-warning w-100 py-2 shadow-sm">SAVE PRODUCT TO INVENTORY</button>
               </form>
            </div>
         </div>

         <div class="col-md-8">
            <div class="card overflow-hidden">
               <table class="table align-middle mb-0">
                  <thead class="table-dark">
                     <tr>
                        <th class="ps-3">Image</th>
                        <th>Name & Type</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                        $select_products = mysqli_query($conn, "SELECT * FROM `products` ORDER BY id DESC") or die('query failed');
                        while($row = mysqli_fetch_assoc($select_products)){
                           $type_class = ($row['type'] == 'digital') ? 'bg-info text-dark' : 'bg-secondary text-white';
                     ?>
                     <tr class="bg-white">
                        <td class="ps-3"><img src="uploaded_img/<?php echo $row['image_url']; ?>" height="45" width="45" style="object-fit: cover;" class="rounded border"></td>
                        <td>
                           <div class="fw-bold"><?php echo $row['name']; ?></div>
                           <span class="badge type-badge <?php echo $type_class; ?>"><?php echo $row['type']; ?></span>
                        </td>
                        <td class="text-success fw-bold">£<?php echo number_format($row['price'], 2); ?></td>
                        <td>
                           <?php if($row['stock_quantity'] < 5): ?>
                              <span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i><?php echo $row['stock_quantity']; ?></span>
                           <?php else: ?>
                              <?php echo $row['stock_quantity']; ?>
                           <?php endif; ?>
                        </td>
                        <td>
                           <a href="admin_products.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Delete this product?');">
                              <i class="fas fa-trash-can"></i>
                           </a>
                        </td>
                     </tr>
                     <?php } ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
function toggleNewInput(val) {
    const newBox = document.getElementById('new_item_box');
    if(val === 'add_new') {
        newBox.style.display = 'block';
        newBox.querySelector('input').setAttribute('required', 'true');
    } else {
        newBox.style.display = 'none';
        newBox.querySelector('input').removeAttribute('required');
    }
}
</script>
</body>
</html>