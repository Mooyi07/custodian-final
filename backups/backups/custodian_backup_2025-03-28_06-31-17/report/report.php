<?php
include '../config/config.php';
include '../config/conn.php';

// Initialize date variables
$start_date = '';
$end_date = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}

// Fetch records from all tables with date filtering
$report_sql = "
    SELECT 'Academic' AS category, item_id, item_name, item_code, item_qty, item_unit, item_brand, date_purchase, item_location, item_life, item_remarks
    FROM academic_item
    WHERE date_purchase BETWEEN ? AND ?
    UNION ALL
    SELECT 'Cleaning' AS category, item_id, item_name, item_code, item_qty, item_unit, item_brand, date_purchase, item_location, item_life, item_remarks
    FROM cleaning_item
    WHERE date_purchase BETWEEN ? AND ?
    UNION ALL
    SELECT 'Equipment' AS category, item_id, item_name, item_code, item_qty, item_unit, item_brand, date_purchase, item_location, item_life, item_remarks
    FROM item_equipment
    WHERE date_purchase BETWEEN ? AND ?
    UNION ALL
    SELECT 'Inventory' AS category, id AS item_id, item_name, item_code, item_qty, item_unit, item_brand, date_purchase, item_location, item_life, item_remarks
    FROM inventory
    WHERE date_purchase BETWEEN ? AND ?
    ORDER BY date_purchase DESC";

$report_stmt = $conn->prepare($report_sql);

if ($report_stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$report_stmt->bind_param("ssssssss", $start_date, $end_date, $start_date, $end_date, $start_date, $end_date, $start_date, $end_date);
$report_stmt->execute();
$report_result = $report_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprehensive Inventory Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none!important;
            }
            .main-content {
                width: 100%;
            }
            .print-header {
                display: block!important;
            }
        }
        .line {
            width: 100%; /* Full width */
            border: none; /* Remove default border */
            border-top: 1px solid #000; /* Add a solid line */
            margin: 10px 0; /* Add some margin */
        }
    </style>
</head>
<body class="d-flex">
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-lg mt-4">
            <div class="print-header d-none text-center">
                <h1 class="text-center">
                    <img src="../img/logo.png" alt="Logo" style="width: 50px; height: auto; margin-right: 10px;">
                    San Ramon Catholic School, Inc.
                </h1>
                <p>Purok Santan Brgy. Su-ay Himamaylan City, Negros Occidental 6108</p>
            </div>
            <h2 class="text-center mb-4">Comprehensive Inventory Report</h2>

            <!-- Date Filter Form -->
            <form method="POST" class="mb-4 no-print">
                <div class="row">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>" required>
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
            <!-- Print Button -->
            <button class="btn btn-secondary mb-4 no-print" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Print Report
            </button>

            <!-- Report Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Item ID</th>
                            <th>Item Name</th>
                            <th>Item Code</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Brand</th>
                            <th>Date of Purchase</th>
                            <th>Location</th>
                            <th>Life</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $report_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['item_code']); ?></td>
                                <td><?php echo htmlspecialchars($row['item_qty']); ?></td>
                                <td><?php echo htmlspecialchars($row['item_unit']); ?></td>
                                <td><?php echo htmlspecialchars($row['item_brand']); ?></td>
                                <td><?php echo htmlspecialchars($row['date_purchase']); ?></td>
                                <td><?php echo htmlspecialchars($row['item_location']); ?></td>
                                <td><?php echo htmlspecialchars($row['item_life']); ?></td>
                                <td><?php echo htmlspecialchars($row['item_remarks']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            


            <br>
            <div class="print-header d-none">
                <h3>__________________</h3>
                <h3>Prepared By</h3>    
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
