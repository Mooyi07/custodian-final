<?php
include '../config/config.php';
include '../config/conn.php';

// Fetch existing categories
$sql = "SELECT * FROM tbl_cat ORDER BY cat_desc";
$result = $conn->query($sql);

// Handle form submission for adding new category
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['new_category'])) {
    $new_category = trim($_POST['new_category']);
    if (!empty($new_category)) {
        $insert_sql = "INSERT INTO tbl_cat (cat_desc) VALUES (?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("s", $new_category);
        if ($stmt->execute()) {
            // Redirect to refresh the page and show the new category
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $error_message = "Error adding category: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Manage Categories</h1>
        
        <!-- Display existing categories -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <table class="w-full">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-600">ID</th>
                        <th class="px-4 py-2 text-left text-gray-600">Category</th>
                        <th class="px-4 py-2 text-left text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['cat_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['cat_desc']); ?></td>
                            <td class="px-4 py-2">
                                <button class="text-red-500 hover:text-red-700 ml-2 delete-category" data-category-id="<?php echo $row['cat_id']; ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Form to add new category -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Add New Category</h2>
            <?php if (isset($error_message)): ?>
                <p class="text-red-500 mb-4"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="" method="POST" class="space-y-4">
                <div>
                    <label for="new_category" class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" id="new_category" name="new_category" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Add Category
                    </button>
                    <button type="button" id="addEquipmentBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-2">
                        Back
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.delete-category').click(function() {
            var categoryId = $(this).data('category-id');
            if (confirm('Are you sure you want to delete this category?')) {
                $.ajax({
                    url: 'delete_category.php',
                    method: 'POST',
                    data: { cat_id: categoryId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload(); // Reload the page to reflect the changes
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while trying to delete the category.');
                    }
                });
            }
        });

        $('#addEquipmentBtn').click(function() {
            window.location.href = 'http://localhost/custodian/purchase/add_equipment.php';
        });
    });
    </script>
</body>
</html>
