<?php
require_once 'db.php';
require_once 'header.php';

// Check if user is admin
if (!is_admin()) {
    header('Location: login.php');
    exit();
}

getHeader('Add New Product');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize_input($conn, $_POST['name']);
    $description = sanitize_input($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $discount = (float)$_POST['discount'];
    $stock = (int)$_POST['stock'];
    $category_id = (int)$_POST['category_id'];
    $is_digital = isset($_POST['is_digital']) ? 1 : 0;
    $sku = sanitize_input($conn, $_POST['sku']);
    
    // Validate price
    if ($price <= 0) {
        $error = "Price must be greater than 0";
    } elseif ($stock < 0) {
        $error = "Stock cannot be negative";
    } else {
        // Generate image URL based on category
        $icons = [
            1 => 'fa-guitar',
            2 => 'fa-drum',
            3 => 'fa-keyboard',
            4 => 'fa-microphone',
            5 => 'fa-headphones'
        ];
        $icon = $icons[$category_id] ?? 'fa-music';
        
        $sql = "INSERT INTO products (name, description, price, discount, stock, category_id, is_digital, sku, icon) 
                VALUES ('$name', '$description', $price, $discount, $stock, $category_id, $is_digital, '$sku', '$icon')";
        
        if (mysqli_query($conn, $sql)) {
            $success = "Product added successfully!";
            
            // Clear form values
            $_POST = array();
        } else {
            $error = "Error adding product: " . mysqli_error($conn);
        }
    }
}

// Get categories
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>

<div class="card">
    <h1 style="text-align: center; margin-bottom: 2rem; color: var(--primary);">
        <i class="fas fa-plus-circle"></i> Add New Product
    </h1>
    
    <?php if (isset($success)): ?>
        <div style="background: #d4edda; 
                    color: #155724; 
                    padding: 1rem; 
                    border-radius: 5px; 
                    margin-bottom: 1.5rem;
                    border-left: 4px solid var(--success);">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            <a href="index.php" style="float: right; color: var(--success);">
                View Products <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div style="background: #f8d7da; 
                    color: #721c24; 
                    padding: 1rem; 
                    border-radius: 5px; 
                    margin-bottom: 1.5rem;
                    border-left: 4px solid var(--danger);">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" style="max-width: 800px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <!-- Left Column -->
            <div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dark);">
                        <i class="fas fa-guitar"></i> Product Name *
                    </label>
                    <input type="text" 
                           name="name" 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                           required
                           style="width: 100%; 
                                  padding: 0.75rem; 
                                  border: 2px solid #ddd; 
                                  border-radius: 5px;
                                  font-size: 1rem;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dark);">
                        <i class="fas fa-barcode"></i> SKU
                    </label>
                    <input type="text" 
                           name="sku" 
                           value="<?php echo htmlspecialchars($_POST['sku'] ?? ''); ?>"
                           style="width: 100%; 
                                  padding: 0.75rem; 
                                  border: 2px solid #ddd; 
                                  border-radius: 5px;
                                  font-size: 1rem;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dark);">
                        <i class="fas fa-pound-sign"></i> Price (£) *
                    </label>
                    <input type="number" 
                           name="price" 
                           step="0.01" 
                           min="0.01" 
                           value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" 
                           required
                           style="width: 100%; 
                                  padding: 0.75rem; 
                                  border: 2px solid #ddd; 
                                  border-radius: 5px;
                                  font-size: 1rem;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dark);">
                        <i class="fas fa-percentage"></i> Discount (%)
                    </label>
                    <input type="number" 
                           name="discount" 
                           min="0" 
                           max="100" 
                           step="1" 
                           value="<?php echo htmlspecialchars($_POST['discount'] ?? '0'); ?>"
                           style="width: 100%; 
                                  padding: 0.75rem; 
                                  border: 2px solid #ddd; 
                                  border-radius: 5px;
                                  font-size: 1rem;">
                </div>
            </div>
            
            <!-- Right Column -->
            <div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dark);">
                        <i class="fas fa-layer-group"></i> Category *
                    </label>
                    <select name="category_id" 
                            required
                            style="width: 100%; 
                                   padding: 0.75rem; 
                                   border: 2px solid #ddd; 
                                   border-radius: 5px;
                                   font-size: 1rem;
                                   background: white;">
                        <option value="">Select Category</option>
                        <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?php echo $cat['id']; ?>"
                                <?php echo ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dark);">
                        <i class="fas fa-boxes"></i> Stock Quantity *
                    </label>
                    <input type="number" 
                           name="stock" 
                           min="0" 
                           value="<?php echo htmlspecialchars($_POST['stock'] ?? '0'); ?>" 
                           required
                           style="width: 100%; 
                                  padding: 0.75rem; 
                                  border: 2px solid #ddd; 
                                  border-radius: 5px;
                                  font-size: 1rem;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dark);">
                        <i class="fas fa-cloud"></i> Product Type
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <input type="checkbox" 
                               name="is_digital" 
                               value="1"
                               <?php echo ($_POST['is_digital'] ?? 0) == 1 ? 'checked' : ''; ?>>
                        Digital Product (Downloadable)
                    </label>
                    <small style="color: var(--gray);">
                        Check this if the product is a digital download (e.g., sheet music, samples)
                    </small>
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--dark);">
                <i class="fas fa-align-left"></i> Description
            </label>
            <textarea name="description" 
                      rows="4"
                      style="width: 100%; 
                             padding: 0.75rem; 
                             border: 2px solid #ddd; 
                             border-radius: 5px;
                             font-size: 1rem;
                             font-family: inherit;"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
            <button type="submit" 
                    style="background: var(--success); 
                           color: white; 
                           border: none; 
                           padding: 0.75rem 2rem; 
                           border-radius: 5px;
                           cursor: pointer;
                           font-size: 1.1rem;
                           font-weight: 600;
                           transition: background 0.3s ease;">
                <i class="fas fa-save"></i> Save Product
            </button>
            
            <a href="index.php" 
               style="background: var(--gray); 
                      color: white; 
                      text-decoration: none; 
                      padding: 0.75rem 2rem; 
                      border-radius: 5px;
                      font-size: 1.1rem;
                      font-weight: 600;
                      transition: background 0.3s ease;">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>