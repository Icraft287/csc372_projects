<?php
/*
    File: footer.php
    Author: Isaac Craft
    Date: March 25, 2026
    Description: Shared footer include for T's Travel.
                 Used by all pages except login.php (which has a stripped-down footer).
                 Set $footer_minimal = true before require_once'ing this file
                 to render the minimal variant (copyright line only, no nav or socials).
*/

$footer_minimal = $footer_minimal ?? false;
?>
    <footer class="footer">
        <?php if (!$footer_minimal): ?>
        <nav class="footer-links" aria-label="Footer navigation">
            <a href="index.php"        class="footer-link">Home</a>
            <a href="about.php"        class="footer-link">About</a>
            <a href="services.php"     class="footer-link">Services</a>
            <a href="destinations.php" class="footer-link">Destinations</a>
            <a href="contact.php"      class="footer-link">Contact</a>
        </nav>
        <?php endif; ?>
        <div class="footer-bottom">
            <p>&copy; 2026 T's Travel. All rights reserved.</p>
            <?php if (!$footer_minimal): ?>
            <div class="social-links">
                <a href="https://www.facebook.com"  class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Visit our Facebook page">Facebook</a>
                <a href="https://www.instagram.com" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Visit our Instagram page">Instagram</a>
                <a href="https://www.twitter.com"   class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Visit our Twitter page">Twitter</a>
            </div>
            <?php endif; ?>
        </div>
    </footer>

    <script src="js/server.js"></script>
</body>
</html>