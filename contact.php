<?php 
include 'config.php';
include 'header.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    /* Hero Section - Matching Product/About pages */
    .contact-hero {
        background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), 
                    url('https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1600&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }

    /* Floating music notes animation */
    .contact-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M20 50 L30 40 L40 50 L50 30 L60 50 L70 35 L80 50" stroke="%23ffc107" fill="none" stroke-width="2"/><circle cx="25" cy="60" r="3" fill="%23ffc107"/><circle cx="45" cy="60" r="3" fill="%23ffc107"/><circle cx="65" cy="60" r="3" fill="%23ffc107"/></svg>');
        background-size: 200px 200px;
        animation: floatNotes 20s linear infinite;
    }

    @keyframes floatNotes {
        from { transform: translateY(0); }
        to { transform: translateY(-100%); }
    }

    .contact-hero h1 {
        font-size: 3.5rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #ffc107;
    }

    /* Info & Form Cards */
    .info-card, .form-card {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        height: 100%;
        border: none;
        transition: transform 0.3s ease;
    }

    .info-card:hover, .form-card:hover {
        transform: translateY(-5px);
    }

    .icon-box {
        background: linear-gradient(45deg, #ffc107, #ff9800);
        color: #000;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        margin-right: 20px;
        font-size: 1.2rem;
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
    }

    /* Form Styling */
    .form-control {
        border: 2px solid #f8f9fa;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        background: #fff;
        border-color: #ffc107;
        box-shadow: 0 0 15px rgba(255, 193, 7, 0.1);
    }

    .btn-send {
        background: linear-gradient(45deg, #1a1a1a, #333);
        color: #ffc107;
        border: none;
        padding: 15px;
        border-radius: 10px;
        font-weight: 700;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }

    .btn-send:hover {
        background: #ffc107;
        color: #000;
        transform: scale(1.02);
        box-shadow: 0 10px 20px rgba(255, 193, 7, 0.3);
    }

    .social-btn {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f8f9fa;
        color: #1a1a1a;
        transition: 0.3s;
        text-decoration: none;
    }

    .social-btn:hover {
        background: #ffc107;
        color: #000;
        transform: rotate(10deg);
    }

    .title-underline {
        width: 50px;
        height: 4px;
        background: #ffc107;
        margin-bottom: 25px;
        border-radius: 2px;
    }
</style>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<div class="contact-hero text-center text-white">
    <div class="container position-relative">
        <h1 data-aos="fade-down">Connect With Us</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="200">Our musical experts are just a message away.</p>
    </div>
</div>

<div class="container py-5 mt-n5">
    <div class="row g-4 justify-content-center">
        
        <div class="col-lg-5" data-aos="fade-right">
            <div class="info-card">
                <h3 class="fw-bold mb-2">Contact Info</h3>
                <div class="title-underline"></div>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <h6 class="mb-0 fw-bold">Our Headquarters</h6>
                        <p class="text-muted mb-0 small">123 Melody Lane, Colombo, Sri Lanka</p>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box"><i class="fa-solid fa-phone"></i></div>
                    <div>
                        <h6 class="mb-0 fw-bold">Hotline</h6>
                        <p class="text-muted mb-0 small">+94 11 234 5678</p>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box"><i class="fa-solid fa-envelope"></i></div>
                    <div>
                        <h6 class="mb-0 fw-bold">Direct Email</h6>
                        <p class="text-muted mb-0 small">support@melodymasters.com</p>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="fw-bold mb-3">Join Our Community</h6>
                <div class="d-flex gap-2">
                    <a href="#" class="social-btn"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="social-btn"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#" class="social-btn"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-6" data-aos="fade-left">
            <div class="form-card">
                <h3 class="fw-bold mb-2">Send Message</h3>
                <div class="title-underline"></div>
                
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Message</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Tell us how we can help..." required></textarea>
                    </div>
                    <button type="submit" name="send" class="btn btn-send w-100 shadow-sm">
                        <i class="fa-solid fa-paper-plane me-2"></i> SEND TO MELODY MASTERS
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true
    });
</script>

<?php include 'footer.php'; ?>