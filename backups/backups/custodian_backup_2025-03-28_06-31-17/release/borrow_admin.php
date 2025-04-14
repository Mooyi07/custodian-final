<?php
include '../config/config.php';
include '../config/conn.php';


// Define categories
$categories = [
    'equipment' => 'item_equipment',
    'medical' => 'medical_item',
    'academic' => 'academic_item',
    'cleaning' => 'cleaning_item'
];

// Fetch all items for borrowing
$all_items_sql = "SELECT 'equipment' AS category, item_id, item_name FROM item_equipment
                  UNION ALL
                  SELECT 'medical' AS category, item_id, item_name FROM medical_item
                  UNION ALL
                  SELECT 'academic' AS category, item_id, item_name FROM academic_item
                  UNION ALL
                  SELECT 'cleaning' AS category, item_id, item_name FROM cleaning_item
                  ORDER BY category, item_name";
$all_items_result = $conn->query($all_items_sql);

$items_by_category = [];
while ($item = $all_items_result->fetch_assoc()) {
    $items_by_category[$item['category']][] = $item;
}

// Fetch all users
$users_sql = "SELECT userId, username FROM users WHERE role != 'admin'";
$users_result = $conn->query($users_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Item - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex vh-100">
        <?php include '../includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4 d-flex align-items-center justify-content-center">
            <div class="container py-5 mt-5"> <!-- Added py-5 and mt-5 -->
                <h3 class="mb-4 text-center">Borrow an Item</h3>

                <!-- Success/Error Message -->
                <?php if (isset($_SESSION['borrow_message'])): ?>
                    <div class="alert alert-<?php echo ($_SESSION['borrow_status'] == 'success' ? 'success' : 'danger'); ?> text-center" role="alert">
                        <strong><?php echo ($_SESSION['borrow_status'] == 'success' ? 'Success!' : 'Error!'); ?></strong> 
                        <?php echo $_SESSION['borrow_message']; ?>
                    </div>
                    <?php unset($_SESSION['borrow_message'], $_SESSION['borrow_status']); ?>
                <?php endif; ?>

                <!-- Borrow Form -->
                <div class="bg-white shadow-lg rounded-lg p-5 mt-4"> <!-- Added p-5 and mt-4 -->
                    <form action="handle_borrow.php" method="POST">
                        <div class="row">
                            <!-- User Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="user" class="form-label">User</label>
                                <select id="user" name="user_id" class="form-select" required>
                                    <option value="">Select a user</option>
                                    <?php while ($user = $users_result->fetch_assoc()): ?>
                                        <option value="<?php echo $user['userId']; ?>">
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Category Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select name="category" id="category" class="form-select" onchange="updateItems()" required>
                                    <option value="">Select a category</option>
                                    <?php foreach ($categories as $category => $table): ?>
                                        <option value="<?php echo $category; ?>"><?php echo ucfirst($category); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Item Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="item" class="form-label">Item</label>
                                <select name="item_id" id="item" class="form-select" required>
                                    <option value="">Select an item</option>
                                </select>
                            </div>

                            <!-- Quantity Input -->
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" name="quantity" id="quantity" min="1" class="form-control" required>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="release.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Borrow
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to Update Items Based on Category -->
    <script>
        function updateItems() {
            const category = document.getElementById('category').value;
            const itemSelect = document.getElementById('item');
            itemSelect.innerHTML = '<option value="">Select an item</option>';

            if (category) {
                const items = <?php echo json_encode($items_by_category); ?>[category];
                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.item_id;
                    option.textContent = item.item_name;
                    itemSelect.appendChild(option);
                });
            }
        }
    </script>
</body>
