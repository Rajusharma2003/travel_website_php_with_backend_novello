<?php
// Set page-specific variables
$pageTitle = "Book Tour";

// Include necessary files
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php?redirect=process_booking.php');
    exit();
}

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Validate CSRF token
if (!validate_csrf_token($_POST['csrf_token'])) {
    // Invalid CSRF token
    $_SESSION['error_message'] = "Invalid request. Please try again.";
    header('Location: index.php');
    exit();
}

// Validate tour ID
$tour_id = filter_input(INPUT_POST, 'tour_id', FILTER_VALIDATE_INT);
if (!$tour_id) {
    $_SESSION['error_message'] = "Invalid tour selection.";
    header('Location: index.php');
    exit();
}

// Validate booking date (datetime-local)
$booking_date = filter_input(INPUT_POST, 'booking_date');
if (!$booking_date) {
    $_SESSION['error_message'] = "Invalid booking date.";
    header('Location: tours/tour_details.php?id=' . $tour_id);
    exit();
}

// Validate travel date
$travel_date = filter_input(INPUT_POST, 'travel_date', FILTER_VALIDATE_REGEXP, 
    ['options' => ['regexp' => '/^\d{4}-\d{2}-\d{2}$/']]);
if (!$travel_date) {
    $_SESSION['error_message'] = "Invalid travel date.";
    header('Location: tours/tour_details.php?id=' . $tour_id);
    exit();
}

// Validate number of people
$number_of_people = filter_input(INPUT_POST, 'number_of_people', FILTER_VALIDATE_INT, 
    ['options' => ['min_range' => 1, 'max_range' => 10]]);
if (!$number_of_people) {
    $_SESSION['error_message'] = "Invalid number of people. Must be between 1 and 10.";
    header('Location: tours/tour_details.php?id=' . $tour_id);
    exit();
}

// Validate total price
$total_price = filter_input(INPUT_POST, 'total_price', FILTER_VALIDATE_FLOAT);
if (!$total_price || $total_price <= 0) {
    $_SESSION['error_message'] = "Invalid total price.";
    header('Location: tours/tour_details.php?id=' . $tour_id);
    exit();
}

// Sanitize special requirements
$special_requirements = filter_input(INPUT_POST, 'special_requirements', FILTER_SANITIZE_STRING);

// Fetch tour details to verify total price calculation
$db = getDbConnection();
$tour_query = "SELECT * FROM tours WHERE tour_id = ?";
$stmt = $db->prepare($tour_query);
$stmt->bind_param("i", $tour_id);
$stmt->execute();
$tour_result = $stmt->get_result();

if ($tour_result->num_rows === 0) {
    $_SESSION['error_message'] = "Tour not found.";
    header('Location: index.php');
    exit();
}

$tour = $tour_result->fetch_assoc();
$expected_total_price = $tour['price'] * $number_of_people;

// Verify total price matches expected
if (abs($total_price - $expected_total_price) > 0.01) {
    $_SESSION['error_message'] = "Price calculation error. Please try again.";
    header('Location: tours/tour_details.php?id=' . $tour_id);
    exit();
}

// Prepare booking insertion
$booking_query = "INSERT INTO bookings (
    user_id, 
    tour_id, 
    booking_date, 
    travel_date, 
    number_of_people, 
    total_price, 
    special_requirements
) VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $db->prepare($booking_query);
$stmt->bind_param("iissids", 
    $_SESSION['user_id'], 
    $tour_id, 
    $booking_date, 
    $travel_date, 
    $number_of_people, 
    $total_price,
    $special_requirements
);

try {
    $stmt->execute();
    
    // Get the last inserted booking ID
    $booking_id = $db->insert_id;
    
    // Send confirmation email (optional)
    $user_email = $_SESSION['user_email']; // Assuming this is set in the session
    $email_subject = "Booking Confirmation - " . $tour['tour_name'];
    $email_body = "Dear {$_SESSION['user_name']},\n\n";
    $email_body .= "Your booking for {$tour['tour_name']} has been received.\n";
    $email_body .= "Booking Details:\n";
    $email_body .= "- Booking Date: $booking_date\n";
    $email_body .= "- Travel Date: $travel_date\n";
    $email_body .= "- Number of People: $number_of_people\n";
    $email_body .= "- Total Price: " . format_price($total_price) . "\n";
    
    if (!empty($special_requirements)) {
        $email_body .= "- Special Requirements: $special_requirements\n";
    }
    
    $email_body .= "\nThank you for your booking!\n";
    
    // Uncomment and configure actual email sending
    // send_email($user_email, $email_subject, $email_body);
    
    // Set success message
    $_SESSION['success_message'] = "Booking successful! Your booking ID is #$booking_id.";
    
    // Redirect to bookings page
    header('Location: profile.php?section=bookings');
    exit();
    
} catch (Exception $e) {
    // Log the error
    error_log("Booking Error: " . $e->getMessage());
    
    // Set error message
    $_SESSION['error_message'] = "An error occurred while processing your booking. Please try again.";
    header('Location: tours/tour_details.php?id=' . $tour_id);
    exit();
}

// Include footer
require_once 'includes/footer.php';
?> 