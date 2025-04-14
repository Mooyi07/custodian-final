<?php
include '../config/config.php';
include '../config/conn.php';

function stockIn($conn, $item_id, $quantity, $purchase_date = null)
{
    if (!is_numeric($item_id) || !is_numeric($quantity) || $quantity <= 0) {
        return ['status' => 'error', 'message' => 'Invalid input'];
    }

    if ($purchase_date === null) {
        $purchase_date = date('Y-m-d');
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update the total item quantity
        $sql = "UPDATE cleaning_item SET item_qty = item_qty + ? WHERE item_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $item_id);
        $stmt->execute();

        // Insert into inventory history with purchase date
        $current_date = date('Y-m-d');
        $sql = "INSERT INTO inventory_history (item_id, quantity_added, date_added, item_type) VALUES (?, ?, ?, 'cleaning')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $item_id, $quantity, $current_date);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        return ['status' => 'success', 'message' => 'Stock updated successfully'];
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        return ['status' => 'error', 'message' => 'Error updating stock: ' . $e->getMessage()];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $purchase_date = isset($_POST['purchase_date']) ? $_POST['purchase_date'] : date('Y-m-d');
    $result = stockIn($conn, $_POST['item_id'], $_POST['quantity'], $purchase_date);
    if ($result['status'] === 'success') {

        
        header('Location: ../stocks/cleaning.php');
        exit();
    } else {
        echo json_encode($result);
    }
} else {
    $sql = "SELECT item_id, item_name FROM cleaning_item ORDER BY item_name";
    $result = $conn->query($sql);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stock In - Cleaning Supplies</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="bg-gray-100 p-6">

        <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl p-6">
            <h2 class="text-2xl font-bold mb-4">Stock In - Cleaning Supplies</h2>
            <form action="" method="post">
                <div class="mb-4">
                    <label for="item_id" class="block text-gray-700 text-sm font-bold mb-2">Select Item:</label>
                    <select name="item_id" id="item_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row["item_id"] . "'>" . htmlspecialchars($row["item_name"]) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="1">
                </div>
                <div class="mb-4">
                    <label for="purchase_date" class="block text-gray-700 text-sm font-bold mb-2">Purchase Date:</label>
                    <input type="date" name="purchase_date" id="purchase_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Stock In
                    </button>
                    <a href="../stocks/cleaning.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </body>

    </html>
<?php
}
$conn->close();
?>