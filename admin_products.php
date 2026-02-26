<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check
if(!isset($_SESSION['user_id']) || strtolower(trim($_SESSION['user_role'] ?? '')) !== 'admin'){
   header('location:login.php');
   exit();
}

// Product add logic
if(isset($_POST['add_product'])){
   $price = $_POST['price'];
   $qty = $_POST['stock'];
   $type = $_POST['product_type']; 
   $description = mysqli_real_escape_string($conn, $_POST['description']);
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

   $pdf_name = "";
   if($type == 'digital' && !empty($_FILES['pdf_file']['name'])){
      $pdf_name = $_FILES['pdf_file']['name'];
      $pdf_tmp_name = $_FILES['pdf_file']['tmp_name'];
      $pdf_folder = 'uploaded_pdf/'.$pdf_name;
      
      if(!is_dir('uploaded_pdf')){ mkdir('uploaded_pdf'); }
      move_uploaded_file($pdf_tmp_name, $pdf_folder);
   }

   if(!empty($name)){
      $add_query = mysqli_query($conn, "INSERT INTO `products`(name, price, stock_quantity, category, image_url, type, description, pdf_url) VALUES('$name', '$price', '$qty', '$category', '$image', '$type', '$description', '$pdf_name')") or die('query failed');
      
      if($add_query){
         if(!is_dir('uploaded_img')){ mkdir('uploaded_img'); }
         move_uploaded_file($image_tmp_name, $image_folder);
         echo "<script>alert('Product Added Successfully!'); window.location.href='admin_products.php';</script>";
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
   <title>Elite Inventory | Melody Masters</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
   
   <style>
   :root { 
      --gold: #D4AF37; 
      --dark-gold: #996515;
      --black: #0b0b0b;
      --glass: rgba(255, 255, 255, 0.95);
      /* Sidebar එකේ පළල අනුව මෙය වෙනස් කරන්න (සාමාන්‍යයෙන් 250px-280px) */
      --sidebar-width: 260px; 
   }

   body { 
      background-color: #f8f8f8; 
      font-family: 'Poppins', sans-serif; 
      color: #333; 
      margin: 0;
      overflow-x: hidden; /* දකුණු පැත්තට scroll වීම වැළැක්වීමට */
   }

   /* Main Content - මුළු Content එකම වම් පැත්තට ගැනීමට */
   .main-content { 
      margin-left: var(--sidebar-width); 
      width: calc(100% - var(--sidebar-width));
      padding: 0 20px 80px 20px; /* වම් පැත්තේ padding අඩු කළා */
      transition: all 0.3s ease;
      margin-top: -80px;
   }

   /* Hero Section */
   .admin-hero {
      background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), 
                  url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1600&q=80');
      background-size: cover; 
      background-position: center;
      padding: 100px 0 140px 0; 
      color: white; 
      text-align: center;
      clip-path: polygon(0 0, 100% 0, 100% 85%, 0% 100%);
      width: 100%;
   }
   .admin-hero h1 { 
      font-family: 'Playfair Display', serif; 
      color: var(--gold); 
      font-size: 2.8rem; 
      letter-spacing: 2px;
      margin-bottom: 10px;
   }

   /* Luxury Form Card */
   .luxury-card {
      background: var(--glass);
      border: none; 
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      backdrop-filter: blur(10px);
      padding: 30px; 
      margin-bottom: 40px;
      border-top: 5px solid var(--gold);
   }

   .form-label { font-weight: 600; color: var(--black); font-size: 0.85rem; text-transform: uppercase; }
   .form-control, .form-select {
      border: 1px solid #ddd; border-radius: 10px; padding: 10px; font-size: 0.9rem;
   }

   /* Gold Button */
   .btn-gold-elite {
      background: linear-gradient(45deg, var(--dark-gold), var(--gold));
      color: white; border: none; padding: 12px; border-radius: 10px;
      font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
      transition: 0.3s; width: 100%; margin-top: 15px;
   }
   .btn-gold-elite:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(212, 175, 55, 0.3); }

   /* Table Styling - වම් පැත්තට වැඩි ඉඩක් ලැබෙන සේ */
   .inventory-container { 
      background: white; 
      border-radius: 20px; 
      box-shadow: 0 15px 35px rgba(0,0,0,0.05); 
      overflow: hidden; 
      width: 100%;
   }
   
   .table thead { background: var(--black); color: var(--gold); }
   .table thead th { 
      padding: 18px 15px; 
      font-size: 0.8rem; 
      letter-spacing: 1px; 
      border: none;
   }
   
   .table tbody td { 
      padding: 15px; 
      vertical-align: middle; 
      border-bottom: 1px solid #f1f1f1;
      font-size: 0.9rem;
   }

   .price-tag { color: var(--dark-gold); font-weight: 700; }
   .stock-badge { padding: 5px 12px; border-radius: 50px; font-weight: 600; font-size: 0.75rem; }
   
   .type-pill {
      background: #f1f1f1; color: #555; padding: 3px 10px; border-radius: 4px;
      font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
   }

   .delete-btn { color: #e74c3c; font-size: 1.1rem; transition: 0.3s; }
   .delete-btn:hover { color: #c0392b; transform: scale(1.1); }

   .section-title {
      font-family: 'Playfair Display', serif; font-size: 1.8rem;
      color: var(--black); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;
   }
   .section-title::after { content: ""; height: 1px; flex-grow: 1; background: #ddd; }

   /* Responsive Adjustments */
   @media (max-width: 992px) {
      .main-content { 
         margin-left: 0; 
         width: 100%; 
         padding: 0 15px 50px 15px; 
      }
      .admin-hero { padding: 60px 0 100px 0; }
   }
</style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<div class="admin-hero">
   <h1>Inventory Management</h1>
   <p class="lead opacity-75">Curate and maintain the finest collection of instruments</p>
</div>

<div class="container main-content">
   
   <div class="luxury-card">
      <h4 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif;"><i class="fas fa-plus-circle me-2 text-warning"></i> Catalog New Masterpiece</h4>
      <form action="" method="post" enctype="multipart/form-data">
         <div class="row g-4">
            <div class="col-md-4">
               <label class="form-label">Instrument / Category</label>
               <select name="product_name" id="productDropdown" class="form-select" onchange="toggleNewInput(this.value)" required>
                  <option value="" disabled selected>Select Category</option>
                  <?php
                     $get_cats = mysqli_query($conn, "SELECT DISTINCT category FROM `products` WHERE category != ''") or die('query failed');
                     while($c_row = mysqli_fetch_assoc($get_cats)){
                        echo "<option value='".$c_row['category']."'>".$c_row['category']."</option>";
                     }
                  ?>
                  <option value="add_new" style="font-weight: bold; color: var(--dark-gold);">+ Create New Category</option>
               </select>
               <div id="new_item_box" class="mt-3" style="display: none;">
                  <input type="text" name="new_item_name" class="form-control" placeholder="Enter New Name">
               </div>
            </div>

            <div class="col-md-4">
               <label class="form-label">Valuation (Rs.)</label>
               <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
            </div>

            <div class="col-md-4">
               <label class="form-label">Availability Type</label>
               <select name="product_type" id="productType" class="form-select" onchange="togglePdfUpload(this.value)" required>
                  <option value="physical">Physical Instrument</option>
                  <option value="digital">Digital Sheet Music</option>
               </select>
            </div>

            <div class="col-md-8">
               <label class="form-label">Artistic Description</label>
               <textarea name="description" class="form-control" rows="3" placeholder="Describe the quality and history..." required></textarea>
            </div>

            <div class="col-md-4">
               <label class="form-label">Initial Stock</label>
               <input type="number" name="stock" class="form-control" placeholder="Units available" required>
            </div>

            <div class="col-md-6">
               <label class="form-label">Primary Visual (Image)</label>
               <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>

            <div class="col-md-6" id="pdf_box" style="display: none;">
               <label class="form-label" style="color: var(--dark-gold);">Sheet Music Attachment (PDF)</label>
               <input type="file" name="pdf_file" class="form-control" accept=".pdf">
            </div>

            <div class="col-12">
               <button type="submit" name="add_product" class="btn-gold-elite shadow">
                  <i class="fas fa-gem me-2"></i> ADD TO OFFICIAL COLLECTION
               </button>
            </div>
         </div>
      </form>
   </div>

   <h2 class="section-title">Global Inventory List</h2>
   
   <div class="inventory-container">
      <div class="table-responsive">
         <table class="table table-hover mb-0">
            <thead>
               <tr>
                  <th class="ps-4">Item Preview</th>
                  <th>Product Essence</th>
                  <th>Valuation</th>
                  <th>Stock Status</th>
                  <th class="text-center">Manage</th>
               </tr>
            </thead>
            <tbody>
               <?php
                  $select_products = mysqli_query($conn, "SELECT * FROM `products` ORDER BY id DESC");
                  if(mysqli_num_rows($select_products) > 0){
                     while($row = mysqli_fetch_assoc($select_products)){
               ?>
               <tr>
                  <td class="ps-4">
                     <img src="uploaded_img/<?php echo $row['image_url']; ?>" width="70" height="70" style="object-fit: cover; border-radius: 15px;" class="shadow-sm border">
                  </td>
                  <td>
                     <div class="fw-bold text-dark" style="font-size: 1.1rem;"><?php echo $row['name']; ?></div>
                     <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="type-pill"><?php echo $row['type']; ?></span>
                        <?php if(!empty($row['pdf_url'])): ?>
                           <i class="fas fa-file-invoice text-danger" title="Digital Asset Attached"></i>
                        <?php endif; ?>
                     </div>
                  </td>
                  <td class="price-tag">Rs. <?php echo number_format($row['price'], 2); ?></td>
                  <td>
                     <?php if($row['stock_quantity'] < 5): ?>
                        <span class="stock-badge bg-danger text-white">CRITICAL: <?php echo $row['stock_quantity']; ?></span>
                     <?php else: ?>
                        <span class="stock-badge bg-success text-white">IN STOCK: <?php echo $row['stock_quantity']; ?></span>
                     <?php endif; ?>
                  </td>
                  <td class="text-center">
                     <a href="admin_products.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Permanently remove this item from the gallery?');">
                        <i class="fas fa-trash-alt"></i>
                     </a>
                  </td>
               </tr>
               <?php 
                     }
                  } else {
                     echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No instruments currently in the inventory.</td></tr>";
                  }
               ?>
            </tbody>
         </table>
      </div>
   </div>
</div>

<script>
function toggleNewInput(val) {
   const newBox = document.getElementById('new_item_box');
   const input = newBox.querySelector('input');
   if(val === 'add_new') {
      newBox.style.display = 'block';
      input.required = true;
      input.focus();
   } else {
      newBox.style.display = 'none';
      input.required = false;
   }
}

function togglePdfUpload(val) {
   const pdfBox = document.getElementById('pdf_box');
   const pdfInput = pdfBox.querySelector('input');
   if(val === 'digital') {
      pdfBox.style.display = 'block';
      pdfInput.required = true;
   } else {
      pdfBox.style.display = 'none';
      pdfInput.required = false;
   }
}
</script>

</body>
</html>