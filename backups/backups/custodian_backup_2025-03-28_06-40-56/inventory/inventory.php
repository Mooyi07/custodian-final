<?php
require_once('../config/config.php');
require_once('../config/conn.php');

if (!isset($_SESSION['isLoggedIn'])) {
    header('location: login.php');
}

// Check if a report type is selected
$reportType = isset($_POST['reportType']) ? $_POST['reportType'] : 'all';

// Base SQL query
$sql = "SELECT 'academic_item' as item_type, item_name, item_code, item_qty, item_unit, item_brand, date_purchase, item_location, item_life, item_remarks, equipment_type FROM `item_equipment`";
$sql .= " UNION ALL ";
$sql .= "SELECT 'Academic' as item_type, item_name, item_code, item_qty, item_unit, item_brand, date_purchase, item_location, item_life, item_remarks, equipment_type FROM `academic_item`";
$sql .= " UNION ALL ";
$sql .= "SELECT 'Cleaning' as item_type, item_name, item_code, item_qty, item_unit, item_brand, date_purchase, item_location, item_life, item_remarks, equipment_type FROM `cleaning_item`";


// Modify the SQL query based on the selected report type
if ($reportType === 'monthly') {
    $sql .= " WHERE MONTH(date_purchase) = MONTH(CURRENT_DATE()) AND YEAR(date_purchase) = YEAR(CURRENT_DATE())";
} elseif ($reportType === 'annual') {
    $sql .= " WHERE YEAR(date_purchase) = YEAR(CURRENT_DATE())";
} elseif ($reportType === 'yearly') {
    // Assuming yearly means all years, no additional filter needed
}

// Order the results
$sql .= " ORDER BY item_name";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
        function searchTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("item_equipment");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let tdArray = tr[i].getElementsByTagName("td");
                let found = false;
                for (let j = 0; j < tdArray.length; j++) {
                    if (tdArray[j] && tdArray[j].innerText.toLowerCase().includes(filter)) {
                        found = true;
                        break;
                    }
                }
                tr[i].style.display = found ? "" : "none";
            }
        }
    </script>
</head>


<body>
        
    <div class="d-flex vh-100">
    <?php include '../includes/sidebar.php'; ?>
        <div class="flex-grow-1 p-4">
            <div class="container py-4">
                <h1 class="mb-4">Inventory Report</h1>
                <form method="POST" class="d-flex justify-content-between mb-3">
                    <button class="btn btn-primary ms-2"><i class="fas fa-sync-alt"></i> Update</button>
                    
                </form>


                <div class="d-flex justify-content-between mb-3">
                    <div class="input-group w-50">
                        <input type="text" id="searchInput" onkeyup="searchTable()" class="form-control" placeholder="Search inventory...">
                    </div>
                </div>
                <div class="bg-white shadow rounded p-2">
                    <table id="item_equipment" class="table table-striped table-bordered ">
                        <thead class="table-dark">
                            <tr>
                                <th>Type</th>
                                <th>Equipment/Item</th>
                                <th>Qty</th>
                                <th>Brand/Model</th>
                                <th>Date Purchase</th>
                                <th>Location</th>
                                <th>Expiration</th>
                                <th>Remarks</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["item_type"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["item_name"]) . "</td>";
                                    echo "<td class='text-center'>" . htmlspecialchars($row["item_qty"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["item_brand"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["date_purchase"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["item_location"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["item_life"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["item_remarks"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["equipment_type"]) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No data available</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>