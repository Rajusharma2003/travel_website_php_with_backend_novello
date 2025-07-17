<?php
include 'includes/header.php';
require_once 'includes/db_connection.php';

$conn = getDbConnection();

// Get list of gallery images
$gallery_query = "SELECT * FROM gallery ORDER BY created_at DESC";
$gallery_result = $conn->query($gallery_query);

$gallery_images = [];
if ($gallery_result && $gallery_result->num_rows > 0) {
    while($row = $gallery_result->fetch_assoc()) {
        $gallery_images[] = $row;
    }
}

closeDbConnection($conn);
?>

<div class="container my-5 pt-4">
    <h2 class="text-center mb-4">Our Gallery</h2>
    <div class="row">
        <?php if (count($gallery_images) > 0): ?>
            <?php foreach ($gallery_images as $image): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($image['image_path'])): ?>
                            <img src="../<?php echo htmlspecialchars($image['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($image['title'] ?? 'Gallery Image'); ?>" style="height: 250px; object-fit: cover;">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/400x250?text=No+Image" class="card-img-top" alt="No Image" style="height: 250px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($image['title'] ?? 'Untitled'); ?></h5>
                            <?php if (!empty($image['description'])): ?>
                                <p class="card-text"><?php echo htmlspecialchars($image['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">No gallery images found at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
