<?php
include '../config/config.php';
include '../config/conn.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['userId']; // Now this should be set

// Fetch all available items from the specified tables
$items_sql = "
    SELECT 'Academic' AS category, item_id, item_name, item_code, item_qty, item_unit, item_brand, date_purchase, item_location, item_life, item_remarks, equipment_type
    FROM academic_item WHERE item_qty > 0 AND equipment_type = 'Equipment'
    UNION ALL
    SELECT 'Cleaning' AS category, item_id, item_name, item_code, item_qty, item_unit, item_brand, date_purchase, item_location, item_life, item_remarks, equipment_type
    FROM cleaning_item WHERE item_qty > 0 AND equipment_type = 'Equipment'
    UNION ALL
    SELECT 'Equipment' AS category, item_id, item_name, item_code, item_qty, item_unit, item_brand, date_purchase, item_location, item_life, item_remarks, equipment_type
    FROM item_equipment WHERE item_qty > 0 AND equipment_type = 'Equipment'
    ORDER BY category, item_name";

$items_result = $conn->query($items_sql);

if ($items_result === false) {
    die('Query failed: ' . htmlspecialchars($conn->error));
}

// Remove or comment out debugging output
// while ($row = $items_result->fetch_assoc()) {
//     echo '<pre>';
//     print_r($row); // Print each row fetched
//     echo '</pre>';
// }

function displayAlert($sessionKey, $successClass, $errorClass) {
    if (isset($_SESSION[$sessionKey])) {
        $status_class = ($_SESSION[$sessionKey] == 'success') ? $successClass : $errorClass;
        echo '<div class="' . $status_class . ' alert-dismissible fade show" role="alert">';
        echo '<span>' . $_SESSION[$sessionKey . '_message'] . '</span>';
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION[$sessionKey]);
        unset($_SESSION[$sessionKey . '_message']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <?php include '../includes/user_sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="main-content p-4">
                    <h1 class="text-3xl font-semibold mb-6">Borrowed Items</h1>

                    <!-- Alert Messages -->
                    <div id="alert-message" class="alert alert-success d-none" role="alert">
                        <!-- Message will be inserted here -->
                    </div>

                    <!-- Borrow Form Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            Borrow an Item
                        </div>
                        <div class="card-body">
                            <form action="handle_borrow.php" method="POST">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select name="category" id="category" class="form-select" required>
                                        <option value="">Select a category</option>
                                        <option value="equipment">Equipment</option>
                                        <option value="academic">Academic</option>
                                        <option value="cleaning">Cleaning</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="item" class="form-label">Item</label>
                                    <select name="item_id" id="item" class="form-select" required>
                                        <option value="">Select an item</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" min="1" class="form-control" required>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-plus me-2"></i>Borrow
                                    </button>
                                    <a href="../request/request_user.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fetch available items from the server
        const itemsByCategory = {
            equipment: [],
            academic: [],
            cleaning: []
        };

        <?php while ($row = $items_result->fetch_assoc()): ?>
            itemsByCategory['<?php echo strtolower($row['category']); ?>'].push({
                item_id: <?php echo $row['item_id']; ?>,
                item_name: "<?php echo $row['item_name']; ?>"
            });
        <?php endwhile; ?>

        const categorySelect = document.getElementById('category');
        const itemSelect = document.getElementById('item');

        categorySelect.addEventListener('change', function() {
            const category = this.value;
            itemSelect.innerHTML = '<option value="">Select an item</option>';
            if (category && itemsByCategory[category]) {
                itemsByCategory[category].forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.item_id;
                    option.textContent = item.item_name;
                    itemSelect.appendChild(option);
                });
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
