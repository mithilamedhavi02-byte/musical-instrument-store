<?php
// includes/footer.php
?>
<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row g-4 text-center text-md-start">
            <div class="col-md-4">
                <h5 class="fw-bold mb-4" style="color: #d4af37;">The Music Shop</h5>
                <p class="text-muted small">The ultimate destination for musicians. Quality instruments at competitive prices since 2010.</p>
                <p class="small text-muted">
                    <i class="fas fa-map-marker-alt me-2"></i> 123 Music Street, Colombo 07, Sri Lanka
                </p>
            </div>
            <div class="col-md-4 text-center">
                <h5 class="fw-bold mb-4">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="text-muted text-decoration-none small">Home</a></li>
                    <li class="mb-2"><a href="products.php" class="text-muted text-decoration-none small">Products</a></li>
                    <li class="mb-2"><a href="about.php" class="text-muted text-decoration-none small">About Us</a></li>
                    <li class="mb-2"><a href="contact.php" class="text-muted text-decoration-none small">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4 text-md-end text-center">
                <h5 class="fw-bold mb-4">Follow Us</h5>
                <div class="d-flex justify-content-center justify-content-md-end gap-3">
                    <a href="#" class="text-white fs-5"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white fs-5"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white fs-5"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="text-white fs-5"><i class="fab fa-whatsapp"></i></a>
                </div>
                <div class="mt-4">
                    <p class="small text-muted">
                        <i class="fas fa-phone me-2"></i> +94 112 345 678
                    </p>
                    <p class="small text-muted">
                        <i class="fas fa-envelope me-2"></i> info@musicshop.lk
                    </p>
                </div>
            </div>
        </div>
        <hr class="my-4 border-secondary opacity-25">
        <p class="text-center text-muted small mb-0">
            &copy; <?= date('Y'); ?> The Music Shop. All rights reserved.
        </p>
    </div>
</footer>