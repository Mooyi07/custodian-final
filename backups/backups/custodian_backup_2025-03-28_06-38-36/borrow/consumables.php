<?php
include '../config/config.php';
include '../config/conn.php';

// Fetch consumables from the database
$consumables_sql = "SELECT * FROM consumables"; // Adjust the table name as necessary
$consumables_result = $conn->query($consumables_sql);

// Handle consumable addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_consumable'])) {
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];

    $add_sql = "INSERT INTO consumables (name, quantity) VALUES (?, ?)";
    $add_stmt = $conn->prepare($add_sql);
    $add_stmt->bind_param("si", $name, $quantity);
    $add_stmt->execute();
    $add_stmt->close();
    header("Location: consumables.php"); // Redirect to the same page
    exit();
}

// Handle consumable deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM consumables WHERE id = ?"; // Adjust the table name and column as necessary
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    header("Location: consumables.php"); // Redirect to the same page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Consumables</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Manage Consumables</h1>

        <!-- Add Consumable Form -->
        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Consumable Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" required>
                </div>
            </div>
            <button type="submit" name="add_consumable" class="btn btn-primary">Add Consumable</button>
        </form>

        <!-- Consumables Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $consumables_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td>
                            <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this consumable?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?> 