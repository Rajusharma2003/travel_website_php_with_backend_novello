<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

// Fetch all messages from the database (table: contact_messages)
try {
    $sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $messages = [];
    $error_message = "Error fetching messages: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Messages</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>User Messages</h2>
                </div>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($messages)): ?>
                                <?php foreach ($messages as $msg): ?>
                                    <tr>
                                        <td><?php echo $msg['message_id']; ?></td>
                                        <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                        <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                        <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($msg['message'])); ?></td>
                                        <td><?php echo $msg['created_at']; ?></td>
                                        <td>
                                            <button class="btn btn-info btn-sm view-btn" data-toggle="modal" data-target="#viewModal" 
                                                data-id="<?php echo $msg['message_id']; ?>"
                                                data-name="<?php echo htmlspecialchars($msg['name']); ?>"
                                                data-email="<?php echo htmlspecialchars($msg['email']); ?>"
                                                data-subject="<?php echo htmlspecialchars($msg['subject']); ?>"
                                                data-message="<?php echo htmlspecialchars($msg['message']); ?>"
                                                data-date="<?php echo $msg['created_at']; ?>">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <a href="delete_message.php?id=<?php echo $msg['message_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this message?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6">No messages found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="viewModalLabel">Message Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p><strong>Name:</strong> <span id="modalName"></span></p>
            <p><strong>Email:</strong> <span id="modalEmail"></span></p>
            <p><strong>Subject:</strong> <span id="modalSubject"></span></p>
            <p><strong>Message:</strong></p>
            <p id="modalMessage"></p>
            <p><strong>Date:</strong> <span id="modalDate"></span></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
      $(document).ready(function() {
        $('.view-btn').on('click', function() {
          $('#modalName').text($(this).data('name'));
          $('#modalEmail').text($(this).data('email'));
          $('#modalSubject').text($(this).data('subject'));
          $('#modalMessage').text($(this).data('message'));
          $('#modalDate').text($(this).data('date'));
        });
      });
    </script>
</body>
</html> 