<?php 
include 'config.php';
include 'header.php'; 

if(isset($_POST['send'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $msg = mysqli_real_escape_string($conn, $_POST['message']);

    $insert = mysqli_query($conn, "INSERT INTO `messages`(name, email, message) VALUES('$name', '$email', '$msg')");
    if($insert){
        echo "<script>alert('Message sent successfully!'); window.location.href='contact.php';</script>";
    }
}
?>

<style>
    /* Hero Section - Image only at the top */
    .contact-hero {
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                    url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');
        background-size: cover;
        background-position: center;
        padding: 100px 0;
        color: white;
        text-align: center;
    }

    /* Details Section - Perfectly below the image */
    .info-card, .form-card {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        height: 100%;
        border: 1px solid #eee;
    }

    .icon-box {
        background: #ffc107;
        color: #000;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 15px;
    }

    .form-control {
        border: 1px solid #ddd;
        padding: 12px;
        margin-bottom: 15px;
    }

    .form-control:focus {
        border-color: #ffc107;
        box-shadow: none;
    }
</style>

<div class="contact-hero">
    <div class="container">
        <h1 class="display-4 fw-bold text-warning">Get In Touch</h1>
        <p class="lead">Have a question? Our musical experts are ready to help.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 justify-content-center">
        
        <div class="col-lg-5">
            <div class="info-card">
                <h3 class="fw-bold mb-4">Contact Details</h3>
                <hr class="mb-4">
                
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <h6 class="mb-0 fw-bold">Our Location</h6>
                        <p class="text-muted mb-0">123 Melody Lane, Colombo, Sri Lanka</p>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box"><i class="fa-solid fa-phone"></i></div>
                    <div>
                        <h6 class="mb-0 fw-bold">Call Us</h6>
                        <p class="text-muted mb-0">+94 11 234 5678</p>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box"><i class="fa-solid fa-envelope"></i></div>
                    <div>
                        <h6 class="mb-0 fw-bold">Email Support</h6>
                        <p class="text-muted mb-0">info@melodymasters.com</p>
                    </div>
                </div>

                <h6 class="fw-bold mt-4 mb-3">Follow Our Socials</h6>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-warning btn-sm rounded-circle"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-warning btn-sm rounded-circle"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="btn btn-warning btn-sm rounded-circle"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-card">
                <h3 class="fw-bold mb-4">Send us a Message</h3>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Your Message</label>
                        <textarea name="message" class="form-control" rows="4" placeholder="How can we help?" required></textarea>
                    </div>
                    <button type="submit" name="send" class="btn btn-warning w-100 fw-bold py-3 shadow-sm">
                        <i class="fa-solid fa-paper-plane me-2"></i> SEND MESSAGE
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>



<?php include 'footer.php'; ?>