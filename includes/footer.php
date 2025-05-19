    </main>
    <footer class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5 col-md-6">
                    <div class="mb-4">
                        <h5 class="mb-3 fw-bold"><i class="fas fa-charging-station me-2"></i>EasyEV Charging</h5>
                        <p class="text-muted">The smart way to find, book and use electric vehicle charging stations. Our network makes EV charging simple, reliable, and accessible.</p>
                    </div>
                    <div class="social-links">
                        <a href="#" class="me-3 text-decoration-none"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="me-3 text-decoration-none"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="me-3 text-decoration-none"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="me-3 text-decoration-none"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h5 class="mb-3 fw-bold">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-decoration-none"><i class="fas fa-angle-right me-2"></i>Home</a></li>
                        <li class="mb-2"><a href="search_locations.php" class="text-decoration-none"><i class="fas fa-angle-right me-2"></i>Find Stations</a></li>
                        <?php if(!isset($_SESSION['user_id'])): ?>
                            <li class="mb-2"><a href="login.php" class="text-decoration-none"><i class="fas fa-angle-right me-2"></i>Login</a></li>
                            <li class="mb-2"><a href="register.php" class="text-decoration-none"><i class="fas fa-angle-right me-2"></i>Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h5 class="mb-3 fw-bold">Services</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-decoration-none"><i class="fas fa-angle-right me-2"></i>Fast Charging</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none"><i class="fas fa-angle-right me-2"></i>Station Locator</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none"><i class="fas fa-angle-right me-2"></i>Mobile App</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none"><i class="fas fa-angle-right me-2"></i>Support</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="mb-3 fw-bold">Contact</h5>
                    <address class="mb-0">
                        <p class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i>123 EV Street, City, Country</p>
                        <p class="mb-2"><i class="fas fa-phone me-2 text-primary"></i>(123) 456-7890</p>
                        <p class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i>info@easyev.com</p>
                        <p class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Mon - Fri: 9AM - 5PM</p>
                    </address>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-md-0">&copy; <?php echo date('Y'); ?> EasyEV Charging. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <a href="#" class="text-decoration-none me-3">Privacy Policy</a>
                        <a href="#" class="text-decoration-none me-3">Terms of Service</a>
                        <a href="#" class="text-decoration-none">FAQ</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html> 