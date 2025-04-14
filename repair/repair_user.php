<?php
include '../config/config.php';
include '../config/conn.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: ../user/user_login.php");
    exit();
}

$user_id = $_SESSION['userId'];
$success_message = '';
$error_message = '';

// Handle repair request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_repair'])) {
    $borrow_id = $_POST['borrow_id'];
    $repair_description = $_POST['repair_description'];
    $request_date = date('Y-m-d H:i:s');

    // Create repair_requests table if it doesn't exist
    $create_table_sql = "CREATE TABLE IF NOT EXISTS repair_requests (
        repair_id INT PRIMARY KEY AUTO_INCREMENT,
        borrow_id INT,
        user_id INT,
        repair_description TEXT,
        request_date DATETIME,
        status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
        FOREIGN KEY (borrow_id) REFERENCES borrow(borrow_id),
        FOREIGN KEY (user_id) REFERENCES users(userId)
    )";

    try {
        $conn->query($create_table_sql);

        // Insert the repair request
        $insert_sql = "INSERT INTO repair_requests (borrow_id, user_id, repair_description, request_date) 
                       VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iiss", $borrow_id, $user_id, $repair_description, $request_date);
        
        if ($stmt->execute()) {
            $success_message = "Repair request submitted successfully!";
        } else {
            $error_message = "Error submitting repair request.";
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch user's borrowed items
$borrowed_items_sql = "SELECT b.borrow_id, b.item_id, b.category, b.quantity, b.borrow_date,
                              CASE 
                                WHEN b.category = 'equipment' THEN e.item_name
                                WHEN b.category = 'academic' THEN a.item_name
                                WHEN b.category = 'cleaning' THEN c.item_name
                              END AS item_name
                       FROM borrow b
                       LEFT JOIN item_equipment e ON b.item_id = e.item_id AND b.category = 'equipment'
                       LEFT JOIN academic_item a ON b.item_id = a.item_id AND b.category = 'academic'
                       LEFT JOIN cleaning_item c ON b.item_id = c.item_id AND b.category = 'cleaning'
                       WHERE b.userId = ? AND b.status = 'borrowed'";

$stmt = $conn->prepare($borrowed_items_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$borrowed_items_result = $stmt->get_result();

// Fetch user's repair requests
$repair_requests_sql = "SELECT r.*, 
                              CASE 
                                WHEN b.category = 'equipment' THEN e.item_name
                                WHEN b.category = 'academic' THEN a.item_name
                                WHEN b.category = 'cleaning' THEN c.item_name
                              END AS item_name
                       FROM repair_requests r
                       JOIN borrow b ON r.borrow_id = b.borrow_id
                       LEFT JOIN item_equipment e ON b.item_id = e.item_id AND b.category = 'equipment'
                       LEFT JOIN academic_item a ON b.item_id = a.item_id AND b.category = 'academic'
                       LEFT JOIN cleaning_item c ON b.item_id = c.item_id AND b.category = 'cleaning'
                       WHERE r.user_id = ?
                       ORDER BY r.request_date DESC";

$stmt = $conn->prepare($repair_requests_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$repair_requests_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Request</title>
    <link rel="stylesheet" href="../asset/css/bootstrap.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 bg-light vh-100">
                <?php include '../includes/user_sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 mt-4">
                <h2>Submit Repair Request</h2>

                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="borrow_id" class="form-label">Select Borrowed Item</label>
                                <select class="form-select" name="borrow_id" id="borrow_id" required>
                                    <option value="">Choose an item...</option>
                                    <?php while ($item = $borrowed_items_result->fetch_assoc()): ?>
                                        <option value="<?php echo $item['borrow_id']; ?>">
                                            <?php echo $item['item_name']; ?> (Borrowed on: <?php echo date('Y-m-d', strtotime($item['borrow_date'])); ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="repair_description" class="form-label">Repair Description</label>
                                <textarea class="form-control" name="repair_description" id="repair_description" rows="3" required
                                          placeholder="Please describe the issue with the item..."></textarea>
                            </div>
                            <button type="submit" name="submit_repair" class="btn btn-primary">Submit Repair Request</button>
                        </form>
                    </div>
                </div>

                <h3>Your Repair Requests</h3>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Description</th>
                                <th>Request Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($request = $repair_requests_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $request['item_name']; ?></td>
                                    <td><?php echo $request['repair_description']; ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($request['request_date'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo match($request['status']) {
                                                'pending' => 'warning',
                                                'approved' => 'info',
                                                'completed' => 'success',
                                                'rejected' => 'danger',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div> <!-- End of Main Content -->
        </div> <!-- End of Row -->
    </div> <!-- End of Container -->

    <script src="../asset/js/bootstrap.bundle.min.js"></script>
</body>

</html>