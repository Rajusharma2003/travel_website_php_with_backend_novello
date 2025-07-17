    </div> <!-- Close main content container -->

    <!-- Footer -->
    <footer class="footer bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-3">Trae Travel</h5>
                    <p class="">Your gateway to unforgettable travel experiences. Explore the world with us!</p>
                    <div class="social-icons mt-3">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white text-decoration-none py-1 d-block">Home</a></li>
                        <li><a href="packages.php" class="text-white text-decoration-none py-1 d-block">Packages</a></li>
                        <li><a href="tours/browse_tours.php" class="text-white text-decoration-none py-1 d-block">Tours</a></li>
                        <li><a href="contact.php" class="text-white text-decoration-none py-1 d-block">Contact</a></li>
                        <li><a href="about.php" class="text-white text-decoration-none py-1 d-block">About Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="text-uppercase mb-3">Contact Us</h5>
                    <p class="">
                        Email: <a href="mailto:support@traetravel.com" class="text-white text-decoration-none">support@traetravel.com</a><br>
                        Phone: <a href="tel:+15551234567" class="text-white text-decoration-none">+1 (555) 123-4567</a><br>
                        Address: 123 Travel Lane, Wanderlust City, World 54321
                    </p>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="text-center">
                <p class=" mb-0">&copy; <?php echo date('Y'); ?> Trae Travel. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    
    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    
    <script>
    $(document).ready(function(){
        $('.featured-tours-carousel').owlCarousel({
            loop: true,
            autoplay: true,
            margin: 20,
            nav: true,
            dots: false,
            responsive:{
                0:{
                    items: 1
                },
                600:{
                    items: 2
                },
                1000:{
                    items: 3
                }
            }
        });
    });
    </script>

    <!-- Page-specific scripts can be added dynamically -->
    <?php if(isset($additionalScripts)): ?>
        <?php foreach($additionalScripts as $script): ?>
            <script src="<?php echo htmlspecialchars($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html> 