    </div> <!-- Close container -->
    
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Marriage Registration System. All rights reserved.</p>
            <p>Contact: akilykaaya@gmail.com | +255 794 872 433</p>
            <div class="footer-links">
                <a href="help.php"><i class="fas fa-question-circle"></i> Help</a>
                <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
                <a href="privacy.php"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
            </div>
        </div>
    </footer>

<script>
    // Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            
            // Toggle icon between bars and times
            const icon = this.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.classList.remove('fas fa-bars');
                icon.classList.add('fas fa-times');
            } else {
                icon.classList.remove('fas fa-times');
                icon.classList.add('fas fa-bars');
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenuToggle.contains(event.target) && !navMenu.contains(event.target)) {
                navMenu.classList.remove('active');
                const icon = mobileMenuToggle.querySelector('i');
                icon.classList.remove('fas fa-times');
                icon.classList.add('fas fa-bars');
            }
        });
        
        // Close menu when clicking on a link
        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                const icon = mobileMenuToggle.querySelector('i');
                icon.classList.remove('fas fa-times');
                icon.classList.add('fas fa-bars');
            });
        });
    }
</script>
</body>
</html>