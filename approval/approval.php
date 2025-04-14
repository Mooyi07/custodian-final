<?php
include '../config/config.php';
include '../config/conn.php';

// Fetch all borrow records grouped by borrower
$borrow_records_sql = "SELECT b.borrow_id, b.userId, u.username, b.item_id, b.category, b.quantity, b.borrow_date, b.status,
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
                         ORDER BY u.username ASC, b.borrow_date DESC";

$borrow_stmt = $conn->prepare($borrow_records_sql);
$borrow_stmt->execute();
$borrow_result = $borrow_stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['requests'] as $borrow_id => $action) {
        if ($action == 'approve' || $action == 'reject') {
            $update_sql = "UPDATE borrow SET status = ? WHERE borrow_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $action, $borrow_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }
    header("Location: approval.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex vh-100">
        <?php include '../includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4">
            <div class="container py-4">
                <h1 class="mb-4">Borrow Requests</h1>
                <form method="POST">
                    <?php 
                    if ($borrow_result->num_rows > 0):
                        $requests = array();
                        
                        // Group requests by borrower
                        while ($row = $borrow_result->fetch_assoc()) {
                            $userId = $row['userId'];
                            $requests[$userId][] = $row;
                        }
                        
                        // Sort borrowers alphabetically
                        ksort($requests);
                        
                        foreach ($requests as $userId => $userRequests):
                            $borrowerName = htmlspecialchars($userRequests[0]['username']);
                    ?>
                    <div class="card mb-3">
                        <div class="card-header" role="button" data-bs-toggle="collapse" 
                             data-bs-target="#user<?php echo $userId; ?>" 
                             aria-expanded="true" aria-controls="user<?php echo $userId; ?>">
                            <h5 class="mb-0">
                                <span class="text-primary"><?php echo $borrowerName; ?></span>
                                <span class="badge bg-secondary ms-2"><?php echo count($userRequests); ?> requests</span>
                            </h5>
                        </div>
                        <div id="user<?php echo $userId; ?>" class="collapse show">
                            <div class="card-body p-0">
                                <table class="table table-bordered m-0">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Category</th>
                                            <th>Quantity</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($userRequests as $request): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($request['item_name']); ?></td>
                                            <td><?php echo ucfirst(htmlspecialchars($request['category'])); ?></td>
                                            <td><?php echo htmlspecialchars($request['quantity']); ?></td>
                                            <td><?php echo date('F j, Y', strtotime($request['borrow_date'])); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $request['status'] == 'pending' ? 'warning' : ($request['status'] == 'approve' ? 'success' : 'danger'); ?>">
                                                    <?php echo ucfirst($request['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if($request['status'] == 'pending'): ?>
                                                <div class="btn-group" role="group">
                                                    <button type="submit" name="requests[<?php echo $request['borrow_id']; ?>]" value="approve" class="btn btn-success btn-sm">Approve</button>
                                                    <button type="submit" name="requests[<?php echo $request['borrow_id']; ?>]" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endforeach;
                    else: 
                    ?>
                    <div class="alert alert-info">No borrow requests found.</div>
                    <?php endif; ?>
                    
                    <?php if ($borrow_result->num_rows > 0): ?>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$borrow_stmt->close();
$conn->close();
?>
