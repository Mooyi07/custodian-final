<?php
include '../config/config.php';
include '../config/conn.php';

$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

// Fetch user-specific borrow records
$user_borrow_sql = "SELECT b.borrow_id, b.userId, u.username, b.item_id, b.category, b.quantity, b.borrow_date, b.status,
                            CASE 
                              WHEN b.category = 'equipment' THEN e.item_name
                              WHEN b.category = 'academic' THEN a.item_name
                              WHEN b.category = 'cleaning' THEN c.item_name
                            END AS item_name
                     FROM borrow b
                     LEFT JOIN users u ON b.userId = u.userId
                     LEFT JOIN item_equipment e ON b.item_id = e.item_id AND b.category = 'equipment'
                     LEFT JOIN academic_item a ON b.item_id = a.item_id AND b.category = 'academic'
                     LEFT JOIN cleaning_item c ON b.item_id = c.item_id AND b.category = 'cleaning'
                     WHERE b.userId = ?
                     ORDER BY b.borrow_date DESC";

$user_stmt = $conn->prepare($user_borrow_sql);
$user_stmt->bind_param("i", $userId);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">Requests for <?php echo htmlspecialchars($_GET['username'] ?? 'User'); ?></h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Borrow Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $user_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['borrow_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($row['category'])); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($row['borrow_date'])); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$user_stmt->close();
$conn->close();
?>
