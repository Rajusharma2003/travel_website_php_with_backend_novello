<?php
include 'includes/header.php';
require_once 'includes/db_connection.php';

$conn = getDbConnection();

$sql = "SELECT t.*, tc.category_name FROM tours t JOIN tour_categories tc ON t.category_id = tc.category_id ORDER BY t.created_at DESC";
$result = $conn->query($sql);

$tours = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tours[] = $row;
    }
}

closeDbConnection($conn);
?>

<div class="container my-5 pt-4">
    <h2 class="text-center mb-4">Our Packages</h2>
    <div class="row">
        <?php if (count($tours) > 0): ?>
            <?php foreach ($tours as $tour): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($tour['featured_image'])): ?>
                            <img src="../admin/<?php echo htmlspecialchars($tour['featured_image']); ?>" class="card-img-top" alt="Tour Image" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top" alt="No Image" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($tour['tour_name']); ?></h5>
                            <p class="card-text"><strong>Category:</strong> <?php echo htmlspecialchars($tour['category_name']); ?></p>
                            <p class="card-text"><strong>Price:</strong> ₹<?php echo number_format($tour['price'], 2); ?></p>
                            <p class="card-text"><strong>Duration:</strong> <?php echo htmlspecialchars($tour['duration']); ?></p>
                            <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($tour['location']); ?></p>
                            <a href="tour_details.php?id=<?php echo htmlspecialchars($tour['tour_id']); ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">No packages found at the moment. Please check back later!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 