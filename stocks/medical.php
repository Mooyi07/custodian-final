<?php
include '../config/config.php';
include '../config/conn.php';

// Fetch items from the medical_item table
$sql = "SELECT item_id, item_name, item_qty, item_unit FROM medical_item ORDER BY item_name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Supplies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-light">
    <div class="d-flex vh-100">
        <?php include '../includes/sidebar.php'; ?>
        <div class="content flex-grow-1 overflow-hidden">
            <div class="container py-4">
                <h1 class="display-4 text-dark mb-4">Medical Supplies</h1>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <a href="../stocks/academic.php" class="btn btn-secondary">Academic Supplies</a>
                    <a href="../stocks/cleaning.php" class="btn btn-secondary">Cleaning Supplies</a>
                    <a href="../stocks/equipment.php" class="btn btn-secondary">Equipment Supplies</a>
                    <a href="../stocks/medical.php" class="btn btn-primary">Medical Supplies</a>
                </div>
                <div class="bg-white shadow rounded overflow-hidden">
                    <table class="table table-striped">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Supplies</th>
                                <th scope="col">Availability</th>
                                <th scope="col">Unit</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["item_name"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["item_qty"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["item_unit"]) . "</td>";
                                    echo "<td class='text-center'>";
                                    echo "<a href='../stockin/medical_stocks.php?item_id=" . $row["item_id"] . "' class='btn btn-info btn-sm'>+/-</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No medical supplies found</td></tr>";
                            }
                            ?>
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
$conn->close();
?>
