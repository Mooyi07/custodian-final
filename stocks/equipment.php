<?php
include '../config/config.php';
include '../config/conn.php';

$searchQuery = isset($_POST['searchQuery']) ? $_POST['searchQuery'] : '';

$sql = "SELECT item_id, item_name, item_qty, item_unit
        FROM item_equipment
        ORDER BY item_name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Supplies</title>
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

        function stockIn(itemId) {
            const quantity = prompt("Enter the quantity to stock in:", "1");
            if (quantity === null || quantity.trim() === "") return;

            fetch('../stocks/stockin/equipment_stocks.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `item_id=${itemId}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        location.reload();
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the stock.');
                });
        }
    </script>
</head>

<body>
    <div class="d-flex vh-100">

        <?php include '../includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4">
            <div class="container py-4">
                <h1 class="mb-4">Equipment Supplies</h1>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <a href="../stocks/academic.php" class="btn btn-secondary">Academic Supplies</a>
                    <a href="../stocks/cleaning.php" class="btn btn-secondary">Cleaning Supplies</a>
                    <a href="../stocks/equipment.php" class="btn btn-primary">Equipment Supplies</a>
                    <a href="../equipment/add_equipment.php" class="btn btn-success">Add Equipment Supply</a>
                </div>
                <form method="POST" class="d-flex mb-4">
                    <input type="text" name="searchQuery" id="searchInput" class="form-control" placeholder="Search supplies..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button type="submit" class="btn btn-outline-secondary ms-2">Search</button>
                </form>
                <div class="bg-white shadow rounded overflow-hidden p-2">
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
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["item_name"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["item_qty"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["item_unit"]) . "</td>";
                                    echo "<td class='text-center'>";
                                    echo "<a href='../stockin/equipment_stocks.php?item_id=" . $row["item_id"] . "' class='btn btn-success btn-sm'>Add</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No equipment supplies found</td></tr>";
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