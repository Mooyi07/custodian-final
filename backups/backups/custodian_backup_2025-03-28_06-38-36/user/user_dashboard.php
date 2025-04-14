<?php

include '../config/config.php';
include '../config/conn.php';

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userId']; // Get the logged-in user's ID
$username = $_SESSION['username'];

// Fetch all borrowed items
$borrowed_items_sql = "SELECT b.borrow_id, b.item_id, b.category, b.quantity, b.borrow_date, b.expected_return_date, b.status,
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
                       ORDER BY b.borrow_date DESC";

$borrowed_stmt = $conn->prepare($borrowed_items_sql);
$borrowed_stmt->execute();
$borrowed_result = $borrowed_stmt->get_result();

if ($borrowed_result === false) {
    echo "Error executing query: " . $conn->error;
    exit();
}

// Count total borrowed items
$total_borrowed = $borrowed_result->num_rows;

// Count pending returns (items with status 'borrowed')
$pending_returns = 0;

// Count approved requests (items with status 'approved' or 'borrowed')
$approved_requests = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5; /* Light background for the dashboard */
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            width: 240px;
            position: fixed;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 15px;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .dashboard-card {
            transition: transform 0.2s;
            border-radius: 0.5rem; /* Rounded corners */
        }
        .dashboard-card:hover {
            transform: scale(1.05); /* Scale effect on hover */
        }
        .table th, .table td {
            vertical-align: middle; /* Center align table content */
        }
        .alert {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050; /* Ensure alert is above other content */
        }
        .content-area {
            margin-left: 250px; /* Align content with the sidebar width */
            padding: 20px; /* Add padding for better spacing */
            width: calc(100% - 250px); /* Adjust width to account for sidebar */
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php include '../includes/user_sidebar.php'; ?> <!-- Include the sidebar here -->

        <!-- Main Content -->
        <div class="content-area">
            <h1 class="h3 mb-4">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            
            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Total Borrowed Items</h5>
                            <p class="card-text display-4"><?php echo $total_borrowed; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Pending Returns</h5>
                            <p class="card-text display-4"><?php echo $pending_returns; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Approved Requests</h5>
                            <p class="card-text display-4"><?php echo $approved_requests; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Borrowed Items -->
            <div class="card">
                <div class="card-body">
                    <h2 class="h5">Borrowed Items</h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Borrow Date</th>
                                <th>Expected Return</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $row_class = '';
                            while ($row = $borrowed_result->fetch_assoc()):
                                $row_class = $row_class === '' ? 'bg-light' : '';
                                if ($row['status'] == 'borrowed') $pending_returns++;
                                if ($row['status'] == 'approved' || $row['status'] == 'borrowed') $approved_requests++;
                            ?>
                            <tr class="<?php echo $row_class; ?>">
                                <td><?php echo htmlspecialchars($row['borrow_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($row['category'])); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($row['borrow_date'])); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($row['expected_return_date'])); ?></td>
                                <td>
                                    <span class="<?php echo getStatusClass($row['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'borrowed'): ?>
                                        <form action="../borrow/handle_return.php" method="POST" class="d-inline">
                                            <input type="hidden" name="borrow_id" value="<?php echo $row['borrow_id']; ?>">
                                            <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                            <input type="hidden" name="category" value="<?php echo $row['category']; ?>">
                                            <input type="hidden" name="quantity" value="<?php echo $row['quantity']; ?>">
                                            <input type="hidden" name="from_dashboard" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                Return
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
function getStatusClass($status) {
    switch ($status) {
        case 'borrowed':
            return 'text-warning';
        case 'approved':
            return 'text-success';
        case 'returned':
            return 'text-info';
        case 'overdue':
            return 'text-danger';
        default:
            return 'text-secondary';
    }
}

$conn->close();

if (isset($_SESSION['return_status'])) {
    $status_class = ($_SESSION['return_status'] == 'success') ? 'alert alert-success' : 'alert alert-danger';
    echo '<div class="alert ' . $status_class . '" role="alert">';
    echo '<span>' . $_SESSION['return_message'] . '</span>';
    echo '</div>';
    unset($_SESSION['return_status']);
    unset($_SESSION['return_message']);
}
?>

<script>
    // Hide the alert after 5 seconds
    setTimeout(function() {
        var alert = document.querySelector('.alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 5000);
</script>