<?php
// contact.php
session_start();
require_once "includes/db.php";

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // Basic validation
    if(empty($name) || empty($email) || empty($message)) {
        $error = "Please fill all required fields";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address";
    } else {
        // Here you would typically save to database or send email
        // For now, we'll just show success message
        $success = "Thank you for your message! We'll contact you soon.";
        unset($_POST);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - The Music Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
         /* White & Gold Theme */
        :root {
            --gold: #D4AF37;
            --dark-gold: #B8860B;
            --black: #0A0A0A;
            --white: #FFFFFF;
            --light-gray: #F9F9F9;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--white);
            color: var(--black);
        }
        
        /* Hero Section */
         .navbar {
            background: var(--black) !important;
            border-bottom: 2px solid var(--gold);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            color: var(--gold) !important;
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
        }
        
        .nav-link {
            color: var(--white) !important;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: var(--gold) !important;
        }
        .contact-hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=1600');
             background-size: cover;
            background-position: center;
            padding: 100px 0 60px 0;
            color: var(--gold);
            text-align: center;
            border-bottom: 4px solid var(--gold);
        }
        .contact-info { border-left: 4px solid #d4af37; padding-left: 20px; }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <section class="contact-hero d-flex align-items-center">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Get in Touch</h1>
            <p class="lead">We're here to help with all your musical needs</p>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Contact Information</h2>
                    
                    <div class="mb-4 contact-info">
                        <h5 class="fw-bold"><i class="fas fa-map-marker-alt me-2"></i> Our Store</h5>
                        <p>123 Music Street, Colombo 07, Sri Lanka</p>
                    </div>
                    
                    <div class="mb-4 contact-info">
                        <h5 class="fw-bold"><i class="fas fa-phone me-2"></i> Phone Numbers</h5>
                        <p>+94 112 345 678 (Store)<br>+94 772 345 678 (WhatsApp)</p>
                    </div>
                    
                    <div class="mb-4 contact-info">
                        <h5 class="fw-bold"><i class="fas fa-envelope me-2"></i> Email</h5>
                        <p>info@musicshop.lk (General)<br>sales@musicshop.lk (Sales)</p>
                    </div>
                    
                    <div class="mb-4 contact-info">
                        <h5 class="fw-bold"><i class="fas fa-clock me-2"></i> Opening Hours</h5>
                        <p>Monday - Friday: 9:00 AM - 7:00 PM<br>Saturday: 9:00 AM - 5:00 PM<br>Sunday: 10:00 AM - 4:00 PM</p>
                    </div>
                    
                    <div class="mt-5">
                        <h5 class="fw-bold mb-3">Follow Us</h5>
                        <div class="d-flex gap-3">
                            <a href="#" class="btn btn-dark rounded-circle"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-dark rounded-circle"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="btn btn-dark rounded-circle"><i class="fab fa-youtube"></i></a>
                            <a href="#" class="btn btn-dark rounded-circle"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-body p-5">
                            <h2 class="fw-bold mb-4">Send us a Message</h2>
                            
                            <?php if($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                            <?php endif; ?>
                            
                            <?php if($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Your Name *</label>
                                        <input type="text" name="name" class="form-control" required 
                                               value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address *</label>
                                        <input type="email" name="email" class="form-control" required 
                                               value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>">
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label">Subject</label>
                                        <input type="text" name="subject" class="form-control" 
                                               value="<?= isset($_POST['subject']) ? $_POST['subject'] : '' ?>">
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label">Message *</label>
                                        <textarea name="message" class="form-control" rows="5" required><?= isset($_POST['message']) ? $_POST['message'] : '' ?></textarea>
                                    </div>
                                    
                                    <div class="col-12 mt-3">
                                        <button type="submit" class="btn btn-primary px-5 py-2">
                                            <i class="fas fa-paper-plane me-2"></i> Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Visit Our Store</h2>
            <div class="row">
                <div class="col-md-8">
                    <!-- Google Map Embed -->
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126743.59360673076!2d79.77455243805943!3d6.922001677008079!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae253d10f7a7003%3A0x320b2e4d32d3838d!2sColombo%2C%20Sri%20Lanka!5e0!3m2!1sen!2s!4v1698765432105!5m2!1sen!2s" 
                                style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded shadow h-100">
                        <h5 class="fw-bold mb-3">Store Features</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Try before you buy</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Expert consultation</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Free parking available</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Air-conditioned showroom</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Restroom facilities</li>
                            <li><i class="fas fa-check text-success me-2"></i> Wheelchair accessible</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include "includes/footer.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>