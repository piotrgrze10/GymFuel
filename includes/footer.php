<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section footer-brand">
                <div class="footer-logo">
                    <i class="fa-solid fa-fire-flame-curved"></i>
                    <span>Gym<span class="blue-text">Fuel</span></span>
                </div>
                <p class="footer-description">
                    Your ultimate fitness companion for tracking nutrition, calories, and achieving your health goals.
                </p>
                <div class="footer-social">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <i class="fa-brands fa-facebook"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Twitter">
                        <i class="fa-brands fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="YouTube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                </div>
            </div>

            <div class="footer-section">
                <h4 class="footer-title">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo $logged_in ? 'dashboard.php' : 'index.php'; ?>">Home</a></li>
                    <li><a href="search_products.php">Search Products</a></li>
                    <li><a href="bmi_calculator.php">Calculators</a></li>
                    <?php if ($logged_in): ?>
                        <li><a href="profile.php">My Profile</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="footer-section">
                <h4 class="footer-title">Support</h4>
                <ul class="footer-links">
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#help">Help Center</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4 class="footer-title">Legal</h4>
                <ul class="footer-links">
                    <li><a href="#privacy">Privacy Policy</a></li>
                    <li><a href="#terms">Terms of Service</a></li>
                    <li><a href="#cookies">Cookie Policy</a></li>
                    <li><a href="#disclaimer">Disclaimer</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-copyright">
                &copy; <?php echo date('Y'); ?> GymFuel. All rights reserved.
            </p>
            <p class="footer-made-with">
                Made with <i class="fa-solid fa-heart"></i> for fitness enthusiasts
            </p>
        </div>
    </div>
</footer>

