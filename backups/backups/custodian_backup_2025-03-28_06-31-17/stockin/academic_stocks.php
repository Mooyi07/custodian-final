<?php
include '../config/config.php';
include '../config/conn.php';

function stockIn($conn, $item_id, $quantity) {
    // Validate input
    if (!is_numeric($item_id) || !is_numeric($quantity) || $quantity <= 0) {
        return ['status' => 'error', 'message' => 'Invalid input'];
    }

    // Update the quantity in the database
    $sql = "UPDATE academic_item SET item_qty = item_qty + ? WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $item_id);

    if ($stmt->execute()) {
        return ['status' => 'success', 'message' => 'Stock updated successfully'];
    } else {
        return ['status' => 'error', 'message' => 'Error updating stock'];
    }
}

$message = '';
$selected_item_id = isset($_GET['item_id']) ? $_GET['item_id'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = stockIn($conn, $_POST['item_id'], $_POST['quantity']);
    $message = $result['message'];
}

// Fetch all academic items for the dropdown
$sql = "SELECT item_id, item_name FROM academic_item ORDER BY item_name";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock In - Academic Supplies</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl p-6">
        <h2 class="text-2xl font-bold mb-4">Stock In - Academic Supplies</h2>
        <?php
        if ($message) {
            echo "<p class='mb-4 p-2 bg-blue-100 text-blue-700 rounded'>{$message}</p>";
        }
        ?>
        <form action="" method="post">
            <div class="mb-4">
                <label for="item_id" class="block text-gray-700 text-sm font-bold mb-2">Select Item:</label>
                <select name="item_id" id="item_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $selected = ($row["item_id"] == $selected_item_id) ? 'selected' : '';
                            echo "<option value='" . $row["item_id"] . "' " . $selected . ">" . htmlspecialchars($row["item_name"]) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity:</label>
                <input type="number" name="quantity" id="quantity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="1">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Stock In
                </button>
                <a href="../stocks/academic.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Back
                </a>
            </div>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
