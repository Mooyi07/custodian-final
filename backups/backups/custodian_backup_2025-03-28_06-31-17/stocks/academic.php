<?php
include '../config/config.php';
include '../config/conn.php';

// Initialize search variable
$searchQuery = isset($_POST['searchQuery']) ? $_POST['searchQuery'] : '';

// Base SQL query
$sql = "SELECT item_name, item_qty as total_qty, item_unit 
        FROM academic_item 
        WHERE item_name LIKE '%" . $conn->real_escape_string($searchQuery) . "%' 
        ORDER BY item_name";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Supplies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    <script>
        function filterTable() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const match = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(searchInput));
                row.style.display = match ? '' : 'none';
            });
        }
    </script>
</head>

<body>
    <div class="d-flex vh-100">

        <?php include '../includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4">
            <div class="container py-4">
                <h1 class="mb-4">Academic Supplies</h1>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <a href="../stocks/academic.php" class="btn btn-primary">Academic Supplies</a>
                    <a href="../stocks/cleaning.php" class="btn btn-secondary">Cleaning Supplies</a>
                    <a href="../stocks/equipment.php" class="btn btn-secondary">Equipment Supplies</a>
                    <a href="../equipment/add_academic.php" class="btn btn-success">Add Academic Supply</a>
                </div>

                <!-- Search Form -->
                <form method="POST" class="d-flex mb-4">
                    <input type="text" name="searchQuery" id="searchInput" class="form-control" placeholder="Search supplies..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button type="submit" class="btn btn-outline-secondary ms-2">Search</button>
                </form>

                <div class="bg-white shadow rounded p-2">
                    <table class="table table-striped">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Supplies</th>
                                <th scope="col">Total Quantity</th>
                                <th scope="col">Unit</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["item_name"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["total_qty"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["item_unit"]) . "</td>";
                                    echo "<td class='text-center'>";
                                    echo "<a href='../equipment/add_academic.php?item_name=" . urlencode($row["item_name"]) . "&item_unit=" . urlencode($row["item_unit"]) . "' class='btn btn-success btn-sm'>Add</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No academic supplies found</td></tr>";
                            }

                            ?>

                        </tbody>
                    </table>
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