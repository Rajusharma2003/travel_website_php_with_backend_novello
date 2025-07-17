<?php
// Set page-specific variables
$pageTitle = "Home";
$additionalStyles = ['assets/css/home.css'];
$additionalScripts = [
    '../assets/js/home.js' // Optional: page-specific JavaScript
];

// Include header
require_once 'includes/header.php';
?>

<!-- Main Content -->
<main class="container-fluid p-0">

    <!-- Hero Carousel -->
    <section id="hero-carousel" class="mb-4">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active" style="background-image: url('https://img.freepik.com/free-photo/panoramic-aerial-shot-california-bixby-bridge-green-hill-near-beautiful-blue-water_181624-49596.jpg?ga=GA1.1.60748560.1748862681&semt=ais_hybrid&w=740');">
                    <div class="carousel-caption text-center">
                        <h1 class="display-4 fw-bold">Explore Breathtaking Destinations</h1>
                        <p class="lead mb-4">Discover the world's most incredible landscapes and cultures</p>
                        <a href="tours/browse_tours.php" class="btn btn-primary btn-lg">View Tours</a>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url('https://img.freepik.com/premium-photo/tourists-posing-front-samui-thailand-shot-with-selective-focus-lens-flare_160117-5308.jpg?w=826');">
                    <div class="carousel-caption text-center">
                        <h1 class="display-4 fw-bold">Adventure Awaits</h1>
                        <p class="lead mb-4">Unforgettable experiences tailored just for you</p>
                        <a href="packages.php" class="btn btn-success btn-lg">Book Now</a>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url('https://img.freepik.com/free-photo/tourist-carrying-baggage_23-2151747383.jpg?ga=GA1.1.60748560.1748862681&semt=ais_hybrid&w=740');">
                    <div class="carousel-caption text-center">
                        <h1 class="display-4 fw-bold">Create Lasting Memories</h1>
                        <p class="lead mb-4">Travel with passion, explore with purpose</p>
                        <a href="contact.php" class="btn btn-info btn-lg">Contact Us</a>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="py-5 about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="about-content-block p-4">
                        <h2 class="display-5 fw-bold mb-4 text-center text-lg-start">About Trae Travel</h2>
                        <p class="lead mb-4 text-center text-lg-start">Trae Travel is dedicated to providing unique and memorable travel experiences. We offer a wide range of tours to suit every traveler's dream, from adventurous expeditions to relaxing getaways.</p>
                        <p class="mb-4 text-center text-lg-start">Our mission is to connect you with the world's most incredible landscapes, cultures, and adventures. We pride ourselves on personalized service, expert guidance, and creating journeys that inspire and delight.</p>
                        <div class="text-center text-lg-start">
                            <a href="about.php" class="btn btn-primary btn-lg">Learn More About Us</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-image-block shadow-lg rounded-3 overflow-hidden">
                        <img src="https://img.freepik.com/premium-psd/travel-social-media-post-template_752850-1349.jpg?ga=GA1.1.60748560.1748862681&semt=ais_hybrid&w=740" alt="About Us - Explore the World" class="img-fluid w-100 h-100 object-fit-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Featured Tours Section -->
    <section id="featured-tours" class="container py-5">
        <h2 class="text-center mb-4">Featured Tours</h2>
        <?php
        // Fetch featured tours from database
        $db = getDbConnection();
        $featured_query = "SELECT * FROM tours WHERE featured = 1 LIMIT 6"; // Increased limit for carousel
        $featured_result = $db->query($featured_query);
        
        if ($featured_result && $featured_result->num_rows > 0):
        ?>
        <div class="owl-carousel owl-theme featured-tours-carousel">
            <?php while ($tour = $featured_result->fetch_assoc()): 
                // Construct absolute image path using BASE_PROJECT_URL
                $image_path = !empty($tour['featured_image'])
                    ? SITE_URL . 'admin/' . ltrim($tour['featured_image'], '/') // Use SITE_URL instead of BASE_PROJECT_URL
                    : SITE_URL . 'user/assets/images/tour-placeholder.jpg';
            ?>
                <div class="tour-item">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo htmlspecialchars($image_path); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($tour['tour_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(Utilities::truncateText($tour['description'], 100)); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h4 text-primary mb-0"><?php echo format_price($tour['price']); ?></span>
                                <a href="tour_details.php?id=<?php echo urlencode($tour['tour_id']); ?>" class="btn btn-outline-primary">View Details</a>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-2"></i><?php echo htmlspecialchars($tour['duration'] ?? 'Flexible'); ?>
                                    <i class="fas fa-map-marker-alt ms-3 me-2"></i><?php echo htmlspecialchars($tour['location'] ?? 'Multiple Locations'); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
            <p class="text-center">No featured tours available at the moment.</p>
        <?php endif; ?>
    </section>

    
    <!-- Packages Section -->
    <section id="packages" class="container py-5 bg-light">
        <h2 class="text-center mb-4">Our Packages</h2>
        <?php
        // Fetch packages from database
        $packages_query = "SELECT * FROM tours LIMIT 6";
        $packages_result = $db->query($packages_query);
        
        if ($packages_result && $packages_result->num_rows > 0):
        ?>
        <div class="row">
            <?php while ($package = $packages_result->fetch_assoc()): 
                // DEBUG: Show the raw featured_image value from DB
                echo "<!-- DB featured_image for package {$package['tour_id']}: '" . htmlspecialchars($package['featured_image']) . "' -->\n";

                // Construct absolute image path using BASE_PROJECT_URL
                $image_path = !empty($package['featured_image'])
                    ? SITE_URL . 'admin/' . ltrim($package['featured_image'], '/') // Use SITE_URL instead of BASE_PROJECT_URL
                    : SITE_URL . 'user/assets/images/tour-placeholder.jpg';

                // DEBUG: Show the final constructed image path
                echo "<!-- Constructed image_path for package {$package['tour_id']}: '" . htmlspecialchars($image_path) . "' -->\n";
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo htmlspecialchars($image_path); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($package['tour_name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($package['tour_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(Utilities::truncateText($package['description'], 100)); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h4 text-primary mb-0"><?php echo format_price($package['price']); ?></span>
                                <a href="tours/tour_details.php?id=<?php echo urlencode($package['tour_id']); ?>" class="btn btn-outline-primary">View Details</a>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-2"></i><?php echo htmlspecialchars($package['duration'] ?? 'Flexible'); ?>
                                    <i class="fas fa-map-marker-alt ms-3 me-2"></i><?php echo htmlspecialchars($package['location'] ?? 'Multiple Locations'); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
            <p class="text-center">No packages available at the moment.</p>
        <?php endif; ?>
    </section>


    <style>
        
.why-choose-us-section-two {
  overflow: hidden;
}
@media (max-width: 576px) {
  .why-choose-us-section-two {
    padding: 0 15px 0 15px;
  }
}
.why-choose-us-section-two .section-title-two .description {
  max-width: 540px;
  width: 100%;
  margin: 0 auto;
}
.why-choose-us-section-two .section-title-two .description P {
  text-transform: capitalize;
}
.why-choose-us-section-two .service-card {
  max-width: 322px;
  width: 100%;
  padding: 28px 20px 28px 38px;
  background-color: rgb(232, 246, 255);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: end;
  gap: 12px;
  margin-bottom: 72px;
}
@media (max-width: 1199px) {
  .why-choose-us-section-two .service-card {
    padding: 28px 15px 28px 15px;
  }
}
@media (max-width: 991px) {
  .why-choose-us-section-two .service-card {
    margin-bottom: 40px;
    justify-content: space-around;
  }
}
@media (max-width: 767px) {
  .why-choose-us-section-two .service-card {
    margin-bottom: 20px;
  }
}
.why-choose-us-section-two .service-card:last-child {
  margin-bottom: 0;
}
.why-choose-us-section-two .service-card.style-2 {
  background-color: rgb(237, 255, 234);
  margin-right: 72px;
}
@media (max-width: 1199px) {
  .why-choose-us-section-two .service-card.style-2 {
    margin-right: 0;
  }
}
.why-choose-us-section-two .service-card.style-2 .icon {
  background-color: rgb(77, 166, 39);
}
.why-choose-us-section-two .service-card.style-3 {
  background-color: rgb(255, 241, 232);
}
.why-choose-us-section-two .service-card.style-3 .icon {
  background-color: rgb(243, 128, 53);
}
.why-choose-us-section-two .service-card.style-4 {
  background-color: rgb(237, 255, 234);
  justify-content: start;
  padding: 28px 38px 28px 20px;
}
.why-choose-us-section-two .service-card.style-4 .icon {
  background-color: rgb(77, 166, 39);
}
.why-choose-us-section-two .service-card.style-5 {
  background-color: rgb(255, 241, 232);
  margin-left: 72px;
  padding: 28px 38px 28px 20px;
}
@media (max-width: 1199px) {
  .why-choose-us-section-two .service-card.style-5 {
    margin-left: 0;
  }
}
.why-choose-us-section-two .service-card.style-5 .icon {
  background-color: rgb(243, 128, 53);
}
.why-choose-us-section-two .service-card.style-6 {
  background-color: rgb(253, 232, 255);
  padding: 28px 38px 28px 20px;
}
.why-choose-us-section-two .service-card.style-6 .icon {
  background-color: rgb(219, 59, 235);
}
.why-choose-us-section-two .service-card .content {
  text-align: end;
}
.why-choose-us-section-two .service-card .content.two {
  text-align: start;
}
.why-choose-us-section-two .service-card .content h3 {
  font-family: "Rubik", serif;
  font-size: 20px;
  font-weight: 500;
  line-height: 48px;
  color: rgb(0, 0, 0);
  margin-bottom: -1px;
}
@media (max-width: 1199px) {
  .why-choose-us-section-two .service-card .content h3 {
    font-size: 17px;
    line-height: 32px;
  }
}
.why-choose-us-section-two .service-card .content p {
  max-width: 182px;
  width: 100%;
  font-family: "Jost", serif;
  font-size: 15px;
  font-weight: 400;
  line-height: 20px;
  color: var(--paragraph-color);
  margin: 0;
}
.why-choose-us-section-two .service-card .icon {
  background-color: rgb(58, 166, 235);
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}
.why-choose-us-section-two .service-card:hover .icon svg {
  animation: zoomIn 0.5s linear;
}
.why-choose-us-section-two .pngtree-big-ballon {
  position: absolute;
  left: -13%;
  bottom: 70%;
  animation: up-down2 2s linear infinite alternate;
}
@keyframes up-down2 {
  0% {
    transform: translateY(35px);
  }
  50% {
    transform: translateY(20px);
  }
  100% {
    transform: translateY(35px);
  }
}
@media (max-width: 1199px) {
  .why-choose-us-section-two .pngtree-big-ballon {
    bottom: 75%;
  }
  .why-choose-us-section-two .pngtree-big-ballon img {
    width: 80%;
  }
}
@media (max-width: 991px) {
  .why-choose-us-section-two .pngtree-big-ballon {
    bottom: 75%;
  }
  .why-choose-us-section-two .pngtree-big-ballon img {
    width: 70%;
  }
}
@media (max-width: 767px) {
  .why-choose-us-section-two .pngtree-big-ballon {
    display: none;
  }
}
.why-choose-us-section-two .pngtree-small-ballon {
  position: absolute;
  right: -19%;
  bottom: 15%;
  animation: up-down2 2s linear infinite alternate;
}
@media (max-width: 1199px) {
  .why-choose-us-section-two .pngtree-small-ballon {
    right: -12%;
  }
}
@media (max-width: 991px) {
  .why-choose-us-section-two .pngtree-small-ballon {
    z-index: -1;
  }
}
@media (max-width: 767px) {
  .why-choose-us-section-two .pngtree-small-ballon {
    display: none;
  }
}
@keyframes up-down2 {
  0% {
    transform: translateY(35px);
  }
  50% {
    transform: translateY(20px);
  }
  100% {
    transform: translateY(35px);
  }
}
.why-choose-us-section-two .long-arrow-one {
  position: absolute;
  left: 0%;
  top: 39%;
  z-index: -1;
  transform: rotate(350deg);
}
.why-choose-us-section-two .long-arrow-two {
  position: absolute;
  left: 1%;
  bottom: 13%;
}
@media (max-width: 991px) {
  .why-choose-us-section-two .long-arrow-two {
    z-index: -1;
  }
}
@media (max-width: 767px) {
  .why-choose-us-section-two .long-arrow-two {
    z-index: -1;
  }
}
.why-choose-us-section-two .long-arrow-three {
  position: absolute;
  right: 0%;
  top: 39%;
  transform: rotate(8deg);
  z-index: -1;
}
.why-choose-us-section-two .long-arrow-four {
  position: absolute;
  right: 1.8%;
  bottom: 14%;
}
@media (max-width: 991px) {
  .why-choose-us-section-two .long-arrow-four {
    z-index: -1;
  }
}
@media (max-width: 767px) {
  .why-choose-us-section-two .long-arrow-four {
    z-index: -1;
  }
}

    </style>
   <!-- why-choose-us-section-two start -->
   <div class="why-choose-us-section-two">
        <div class="container position-relative">
            <div class="row mb-50">
                <div class="col-lg-12 wow animate fadeInUp" data-wow-delay="200ms" data-wow-duration="1500ms">
                    <div class="section-title-two text-center">
                        <div class="section-title-two">
                            <div class="sub-title-two">
                                <span class="text-black" >Why Choose Us</span>
                            </div>
                            <div class="title">
                                <h2>Your Trusted Travel Partner</h2>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center g-4">
                <div class="col-lg-4 col-md-6 d-flex align-items-xl-end align-items-center flex-column">
                    <div class="service-card wow animate fadeInLeft" data-wow-delay="200ms" data-wow-duration="1500ms">
                        <div class="content">
                            <h3>24/7 Support</h3>
                            <p>
                                Always here to help, anytime you need us — before, during, and after your trip.
                            </p>
                        </div>
                        <div class="icon">
                           <img src="https://img.icons8.com/?size=80&id=7HvEODIem49E&format=png" alt="">
                        </div>
                    </div>
                    <div class="service-card style-2 wow animate fadeInLeft" data-wow-delay="400ms"
                        data-wow-duration="1500ms">
                        <div class="content">
                            <h3>Trusted Service</h3>
                            <p>
                                Delivering reliable travel experiences backed by years of expertise and happy customers.
                            </p>
                        </div>
                        <div class="icon">
                            <img src="https://img.icons8.com/?size=80&id=UGnJlcJuyfNl&format=png" alt="">
                        </div>
                    </div>
                    <div class="service-card style-3 wow animate fadeInLeft" data-wow-delay="600ms"
                        data-wow-duration="1500ms">
                        <div class="content">
                            <h3>Easy Booking</h3>
                            <p>
                                Seamless and hassle-free booking process designed to get you going in just a few clicks.
                            </p>
                        </div>
                        <div class="icon">
                           <img src="https://img.icons8.com/?size=80&id=ALBdV26BC7r1&format=png" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="travel-man">
                        <img src="assets/images/banner/chooseimg.png" alt="" />
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 d-flex align-items-xl-start align-items-center flex-column">
                    <div class="service-card style-4 wow animate fadeInRight" data-wow-delay="200ms"
                        data-wow-duration="1500ms">
                        <div class="icon">
                           <img src="https://img.icons8.com/?size=80&id=QhZWf8kyIqQ1&format=png" alt="">
                        </div>
                        <div class="content two">
                            <h3>Affordable Packages</h3>
                            <p>
                                High-quality travel experiences at prices that fit your budget.
                            </p>
                        </div>
                    </div>
                    <div class="service-card style-5 wow animate fadeInRight" data-wow-delay="400ms"
                        data-wow-duration="1500ms">
                        <div class="icon">
                            <img src="https://img.icons8.com/?size=60&id=T1VWTImcX7a6&format=png" alt="">
                        </div>
                        <div class="content two">
                            <h3>Destination Experts</h3>
                            <p>
                                Tailor-made itineraries to suit your preferences and needs.
                            </p>
                        </div>
                    </div>
                    <div class="service-card style-6 wow animate fadeInRight" data-wow-delay="600ms"
                        data-wow-duration="1500ms">
                        <div class="icon">
                            <img src="https://img.icons8.com/?size=60&id=fXo88IjR5YOL&format=png" alt="">
                        </div>
                        <div class="content two">
                            <h3>Exclusive Offers</h3>
                            <p>
                               Local knowledge and expert guidance to enhance every part of your journey.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
          
        </div>
    </div>
    <!-- why-choose-us-section-two end -->

    <!-- Testimonials Section -->
    <section id="testimonials" class="container py-5">
        <h2 class="text-center mb-4">What Our Travelers Say</h2>
        <?php
        // Simulated testimonial data
        $testimonials = [
            [
                'text' => 'Absolutely incredible experience! Trae Travel made my dream vacation a reality.',
                'author' => 'Jane Doe'
            ],
            [
                'text' => 'Professional, organized, and truly unforgettable. Highly recommend!',
                'author' => 'John Smith'
            ],
            [
                'text' => 'The best travel experience I\'ve ever had. Can\'t wait to book my next trip!',
                'author' => 'Emily Johnson'
            ]
        ];
        ?>
        <div class="row">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <p class="card-text fst-italic">"<?php echo htmlspecialchars($testimonial['text']); ?>"</p>
                            <h5 class="card-title">- <?php echo htmlspecialchars($testimonial['author']); ?></h5>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 ">

                    <form id="contact-form" method="post" action="contact.php">
            <h2 class="text-center mb-4">Contact Us</h2>

                        <?php 
                        // Generate CSRF token
                        $csrf_token = generate_csrf_token(); 
                        ?>
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="message" rows="5" placeholder="Your Message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Message</button>
                    </form>
                </div>

                <!-- map section -->
                <div class="col-md-6">
                <iframe class="rounded-4" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d86986.65716865563!2d77.92878249048452!3d30.454788174194206!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3908d0cfa61cda5b%3A0x197fd47d980e85b1!2sMussoorie%2C%20Uttarakhand!5e1!3m2!1sen!2sin!4v1752144744440!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
// Include footer
require_once 'includes/footer.php';
?> 