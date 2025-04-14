<?php
include '../config/config.php';
include '../config/conn.php';

// Check if user is logged in
if (!isset($_SESSION['userId']) || $_SESSION['role'] != 1) {
    header("Location: user_login.php");
    exit();
}

$user_id = $_SESSION['userId'];
$message = '';
$error = '';

// Fetch borrowed items for the user
$stmt = $conn->prepare("
    SELECT b.borrow_id, b.borrow_date AS borrowed_date, b.expected_return_date AS return_date, b.status,
           CASE 
             WHEN b.category = 'equipment' THEN e.item_name
             WHEN b.category = 'medical' THEN m.item_name
             WHEN b.category = 'academic' THEN a.item_name
             WHEN b.category = 'cleaning' THEN c.item_name
           END AS item_name
    FROM borrow b
    LEFT JOIN item_equipment e ON b.item_id = e.item_id AND b.category = 'equipment'
    LEFT JOIN medical_item m ON b.item_id = m.item_id AND b.category = 'medical'
    LEFT JOIN academic_item a ON b.item_id = a.item_id AND b.category = 'academic'
    LEFT JOIN cleaning_item c ON b.item_id = c.item_id AND b.category = 'cleaning'
    WHERE b.user_id = ?
");
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$borrowed_items = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowed Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex">
        <!-- Sidebar -->
        <?php include '../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4" style="margin-left: 250px;">
            <h1 class="text-3xl font-semibold mb-6">Borrowed Items</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Borrowed Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($borrowed_items) > 0): ?>
                            <?php foreach ($borrowed_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['borrowed_date']); ?></td>
                                    <td><?php echo htmlspecialchars($item['return_date']); ?></td>
                                    <td><?php echo htmlspecialchars($item['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No borrowed items found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>