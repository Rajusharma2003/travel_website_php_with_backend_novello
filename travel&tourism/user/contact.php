<?php
include 'includes/header.php';
?>

<div class="container my-5 pt-4">
    <h2 class="text-center mb-4">Contact Us</h2>
    <div class="row">
        <div class="col-md-8 mx-auto">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <p class="text-center lead mb-4">We'd love to hear from you! Please fill out the form below or reach out to us using the contact details provided.</p>
            
            <form action="process_contact.php" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Your Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Your Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                </div>
            </form>
            
            <hr class="my-5">

            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm p-3">
                        <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                        <h5>Our Office</h5>
                        <p>123 Travel Lane, Adventure City, World 78901</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm p-3">
                        <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                        <h5>Email Us</h5>
                        <p><a href="mailto:info@traetravel.com">info@traetravel.com</a></p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm p-3">
                        <i class="fas fa-phone-alt fa-3x text-primary mb-3"></i>
                        <h5>Call Us</h5>
                        <p><a href="tel:+1234567890">+1 234 567 890</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map Section -->
<section class="map-section mb-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="text-center mb-4">Find Us on the Map</h3>
                <div class="map-container ratio ratio-21x9">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1dYOUR_LATITUDE!2dYOUR_LONGITUDE!3dYOUR_ZOOM!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTPCsDQ5JzAxLjEiTiAxMDPCsDAwJzAwLjAiVw!5e0!3m2!1sen!2sus!4v1678901234567!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>
