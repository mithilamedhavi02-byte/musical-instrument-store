    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <!-- Company Info -->
                <div class="col-lg-4 mb-4">
                    <h5 class="text-gold mb-3">
                        <i class="fas fa-guitar me-2"></i>Melody Masters
                    </h5>
                    <p class="text-muted">
                        Sri Lanka's premier destination for musical instruments, equipment, and services.
                        Quality instruments at competitive prices since 2010.
                    </p>
                    <div class="social-icons mt-4">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-youtube fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-whatsapp fa-lg"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-gold mb-3">Shop</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/shop/" class="text-muted">All Products</a></li>
                        <li class="mb-2"><a href="/shop/category.php?id=1" class="text-muted">Guitars</a></li>
                        <li class="mb-2"><a href="/shop/category.php?id=5" class="text-muted">Keyboards</a></li>
                        <li class="mb-2"><a href="/shop/category.php?id=9" class="text-muted">Drums</a></li>
                        <li class="mb-2"><a href="/shop/category.php?id=13" class="text-muted">Accessories</a></li>
                    </ul>
                </div>
                
                <!-- Services -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-gold mb-3">Services</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/pages/services.php#repair" class="text-muted">Instrument Repair</a></li>
                        <li class="mb-2"><a href="/pages/services.php#rental" class="text-muted">Equipment Rental</a></li>
                        <li class="mb-2"><a href="/pages/services.php#lessons" class="text-muted">Music Lessons</a></li>
                        <li class="mb-2"><a href="/pages/services.php#setup" class="text-muted">Custom Setup</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-4 mb-4">
                    <h6 class="text-gold mb-3">Contact Us</h6>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2 text-gold"></i>
                            123 Music Street, Colombo 07, Sri Lanka
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2 text-gold"></i>
                            +94 112 345 678
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2 text-gold"></i>
                            info@melodymasters.lk
                        </li>
                        <li>
                            <i class="fas fa-clock me-2 text-gold"></i>
                            Mon-Sat: 9:00 AM - 7:00 PM
                        </li>
                    </ul>
                    <div class="mt-4">
                        <h6 class="text-gold mb-2">Newsletter</h6>
                        <form class="d-flex">
                            <input type="email" class="form-control me-2" placeholder="Your email">
                            <button class="btn btn-gold" type="submit">Subscribe</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: #444;">
            
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        &copy; <?= date('Y'); ?> Melody Masters Instrument Shop. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="/pages/terms.php" class="text-muted me-3">Terms & Conditions</a>
                    <a href="/pages/privacy.php" class="text-muted me-3">Privacy Policy</a>
                    <a href="/pages/faq.php" class="text-muted">FAQ</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="/assets/js/main.js"></script>
    
    <!-- Page specific JavaScript -->
    <?php if (isset($page_js)): ?>
    <script><?php echo $page_js; ?></script>
    <?php endif; ?>
</body>
</html>