<?php 
include_once 'config.php';
include_once 'header.php'; 

// User login wela neththan login page ekata yawanna
if(!isset($_SESSION['user_id'])){
   header('location:login.php');
   exit();
}

$user_id = $_SESSION['user_id'];
// Userge details database eken ganeema
$select_user = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$user_id'") or die('query failed');
if(mysqli_num_rows($select_user) > 0){
   $fetch_user = mysqli_fetch_assoc($select_user);
}
?>

<style>
    .dashboard-container { min-height: 80vh; background-color: #f8f9fa; padding: 40px 0; }
    .profile-card { border: none; border-radius: 15px; overflow: hidden; }
    .profile-header { background: linear-gradient(45deg, #1a1a1a, #333); color: white; padding: 30px; text-align: center; }
    .profile-avatar {
        width: 100px; height: 100px;
        background-color: #ffc107; color: #000;
        font-size: 2.5rem; font-weight: bold;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; margin: 0 auto 15px;
        border: 4px solid rgba(255, 255, 255, 0.2);
    }
    .info-label { color: #6c757d; font-size: 0.9rem; margin-bottom: 2px; }
    .info-value { font-weight: 600; color: #1a1a1a; margin-bottom: 20px; }
</style>

<div class="dashboard-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card profile-card shadow">
                    <div class="profile-header">
                        <?php 
                            $email = $_SESSION['user_email'] ?? 'U';
                            $initial = strtoupper(substr($email, 0, 1));
                        ?>
                        <div class="profile-avatar shadow-sm"><?php echo $initial; ?></div>
                        <h3 class="mb-1"><?php echo $fetch_user['name'] ?? 'User Name'; ?></h3>
                        <p class="text-warning mb-0"><i class="fa-solid fa-circle-check me-1"></i> Verified Musician</p>
                    </div>

                    <div class="card-body p-4 bg-white">
                        <div class="row">
                            <div class="col-sm-6 text-center text-sm-start border-end">
                                <p class="info-label">Full Name</p>
                                <p class="info-value"><?php echo $fetch_user['name'] ?? 'Not set'; ?></p>

                                <p class="info-label">Email Address</p>
                                <p class="info-value"><?php echo $_SESSION['user_email'] ?? 'Not set'; ?></p>
                            </div>
                            <div class="col-sm-6 text-center text-sm-start ps-sm-4">
                                <p class="info-label">Member Since</p>
                                <p class="info-value">2026</p>

                                <p class="info-label">Account Type</p>
                                <p class="info-value">Standard Member</p>
                            </div>
                        </div>
                        </div>
                </div>

                <?php if(isset($_SESSION['admin_id'])): ?>
                <div class="mt-4 text-center">
                    <a href="admin.php" class="text-decoration-none text-dark fw-bold">
                        <i class="fa-solid fa-lock text-warning me-1"></i> Switch to Admin Dashboard
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>