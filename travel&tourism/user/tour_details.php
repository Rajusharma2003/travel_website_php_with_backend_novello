<?php
// Set page-specific variables
$pageTitle = "Tour Details";
$additionalStyles = [
    './assets/css/home.css',
    './assets/css/tour_details.css'
];

// Include header
require_once './includes/header.php';

// Check if tour ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to packages page if no ID is provided
    header('Location: ./packages.php');
    exit();
}

// Sanitize tour ID
$tour_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$tour_id) {
    // Invalid tour ID
    header('Location: ./packages.php');
    exit();
}

// Fetch tour details from database
$db = getDbConnection();
$tour_query = "SELECT * FROM tours WHERE tour_id = ?";
$stmt = $db->prepare($tour_query);
$stmt->bind_param("i", $tour_id);
$stmt->execute();
$tour_result = $stmt->get_result();

if ($tour_result->num_rows === 0) {
    // No tour found
    header('Location: ./packages.php');
    exit();
}

$tour = $tour_result->fetch_assoc();

// Fetch itinerary details from tour_itinerary table
$itinerary_query = "SELECT day_number, title, description FROM tour_itinerary WHERE tour_id = ? ORDER BY day_number ASC";
$itinerary_stmt = $db->prepare($itinerary_query);
$itinerary_stmt->bind_param("i", $tour_id);
$itinerary_stmt->execute();
$itinerary_result = $itinerary_stmt->get_result();
$itinerary_details = $itinerary_result->fetch_all(MYSQLI_ASSOC);
$itinerary_stmt->close();

// Construct image path
$image_path = !empty($tour['featured_image'])
    ? SITE_URL . 'admin/' . ltrim($tour['featured_image'], '/')
    : SITE_URL . 'user/assets/images/tour-placeholder.jpg';

// Fetch tour details from tour_details table
$tour_details_query = "SELECT inclusion, exclusion FROM tour_details WHERE tour_id = ?";
$tour_details_stmt = $db->prepare($tour_details_query);
$tour_details_stmt->bind_param("i", $tour_id);
$tour_details_stmt->execute();
$tour_details_result = $tour_details_stmt->get_result();
$tour_details = $tour_details_result->fetch_assoc();
$tour_details_stmt->close();
?>

<main class="container tour-details-page py-5">
    <div class="row pt-5">
        <div class="col-lg-8">
            <div class="tour-main-image mb-4">
                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>" class="img-fluid rounded-3 w-100">
            </div>

            <div class="tour-details-content">
                <h1 class="display-5 mb-3"><?php echo htmlspecialchars($tour['tour_name']); ?></h1>
                
                <div class="tour-meta-info d-flex justify-content-between mb-4">
                    <div class="tour-duration">
                        <i class="fas fa-calendar me-2"></i>
                        <strong>Duration:</strong> <?php echo htmlspecialchars($tour['duration'] ?? 'Flexible'); ?>
                    </div>
                    <div class="tour-location">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <strong>Location:</strong> <?php echo htmlspecialchars($tour['location'] ?? 'Multiple Locations'); ?>
                    </div>
                    <div class="tour-price">
                        <i class="fas fa-tag me-2"></i>
                        <strong>Price:</strong> <?php echo format_price($tour['price']); ?>
                    </div>
                </div>

                <div class="tour-description">
                    <h3>About This Tour</h3>
                    <p><?php echo nl2br(htmlspecialchars($tour['description'])); ?></p>
                </div>


                <div class="tour-booking mt-4">
                    <h3>Book This Tour</h3>
                    <form id="booking-form" method="POST" action="./process_booking.php">
                        <?php 
                        // Generate CSRF token
                        $csrf_token = generate_csrf_token(); 
                        ?>
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="booking_date" class="form-label">Booking Date</label>
                                <input type="datetime-local" class="form-control" id="booking_date" name="booking_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="travel_date" class="form-label">Travel Date</label>
                                <input type="date" class="form-control" id="travel_date" name="travel_date" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="number_of_people" class="form-label">Number of People</label>
                                <input type="number" class="form-control" id="number_of_people" name="number_of_people" min="1" max="10" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="total_price" class="form-label">Total Price</label>
                                <input type="number" step="0.01" class="form-control" id="total_price" name="total_price" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="special_requirements" class="form-label">Special Requirements</label>
                            <textarea class="form-control" id="special_requirements" name="special_requirements" rows="3" placeholder="Any special needs or additional requests"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Book Now</button>
                    </form>
                </div>

                <!-- This is the tour inclusion and exclusion section -->
                <?php if (!empty($tour_details['inclusion']) || !empty($tour_details['exclusion'])): ?>
                <div class="tour-details-section mt-4">
                    <h3>Tour Details</h3>
                    <div class="row">
                        <?php if (!empty($tour_details['inclusion'])): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h4 class="mb-0">What's Included</h4>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <?php 
                                        $inclusions = explode("\n", $tour_details['inclusion']);
                                        foreach ($inclusions as $inclusion): 
                                            if (trim($inclusion) !== ''):
                                        ?>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <?php echo htmlspecialchars(trim($inclusion)); ?>
                                            </li>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($tour_details['exclusion'])): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-danger text-white">
                                    <h4 class="mb-0">What's Not Included</h4>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <?php 
                                        $exclusions = explode("\n", $tour_details['exclusion']);
                                        foreach ($exclusions as $exclusion): 
                                            if (trim($exclusion) !== ''):
                                        ?>
                                            <li class="mb-2">
                                                <i class="fas fa-times-circle text-danger me-2"></i>
                                                <?php echo htmlspecialchars(trim($exclusion)); ?>
                                            </li>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>




                 <!-- This is the itinerary section -->
                 <?php if (!empty($itinerary_details)): ?>
                <div class="tour-itinerary mt-4">
                    <h3>Detailed Itinerary</h3>
                    <div class="accordion" id="tourItineraryAccordion">
                        <?php foreach ($itinerary_details as $index => $item): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDay<?php echo $item['day_number']; ?>">
                                <button class="accordion-button <?php echo $index === 0 ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDay<?php echo $item['day_number']; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapseDay<?php echo $item['day_number']; ?>">
                                    Day <?php echo htmlspecialchars($item['day_number']); ?>: <?php echo htmlspecialchars($item['title']); ?>
                                </button>
                            </h2>
                            <div id="collapseDay<?php echo $item['day_number']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="headingDay<?php echo $item['day_number']; ?>" data-bs-parent="#tourItineraryAccordion">
                                <div class="accordion-body">
                                    <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <script>
                // Dynamic price calculation
                document.addEventListener('DOMContentLoaded', function() {
                    const numberInput = document.getElementById('number_of_people');
                    const totalPriceInput = document.getElementById('total_price');
                    const tourPrice = <?php echo $tour['price']; ?>;

                    numberInput.addEventListener('input', function() {
                        const numberOfPeople = parseInt(this.value) || 0;
                        const totalPrice = (tourPrice * numberOfPeople).toFixed(2);
                        totalPriceInput.value = totalPrice;
                    });

                    // Trigger initial calculation
                    numberInput.dispatchEvent(new Event('input'));
                });
                </script>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="tour-sidebar sticky-top">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Quick Information</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Guaranteed Lowest Price
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Free Cancellation
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Best Price Guarantee
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-success me-2"></i>
                                24/7 Customer Support
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">Need Help?</h4>
                    </div>
                    <div class="card-body">
                        <p>Our travel experts are ready to help you plan your perfect trip.</p>
                        <a href="./contact.php" class="btn btn-outline-primary w-100">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Include footer
require_once './includes/footer.php';
?> 