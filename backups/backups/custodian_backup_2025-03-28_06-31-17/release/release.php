<?php

include '../config/config.php';
include '../config/conn.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userId']; // Get the logged-in user's ID

// Fetch all borrowed items
$borrowed_items_sql = "SELECT b.borrow_id, b.item_id, b.category, b.quantity, b.borrow_date, 
                              b.expected_return_date, b.status, u.username AS user_name,
                              CASE 
                                WHEN b.category = 'equipment' THEN e.item_name
                                WHEN b.category = 'academic' THEN a.item_name
                                WHEN b.category = 'cleaning' THEN c.item_name
                              END AS item_name
                       FROM borrow b
                       JOIN users u ON b.userId = u.userId
                       LEFT JOIN item_equipment e ON b.item_id = e.item_id AND b.category = 'equipment'
                       LEFT JOIN academic_item a ON b.item_id = a.item_id AND b.category = 'academic'
                       LEFT JOIN cleaning_item c ON b.item_id = c.item_id AND b.category = 'cleaning'
                       ORDER BY b.borrow_date DESC";

$borrowed_stmt = $conn->prepare($borrowed_items_sql);
$borrowed_stmt->execute();
$borrowed_result = $borrowed_stmt->get_result();

// Define categories
$categories = [
    'equipment' => 'item_equipment',
    'academic' => 'academic_item',
    'cleaning' => 'cleaning_item'
];

// Fetch all items for borrowing
$all_items_sql = "SELECT 'equipment' AS category, item_id, item_name FROM item_equipment
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id']; // Assuming you have the user's ID in the session

    // Add your logic to insert the borrow record into the database
    $borrow_date = date('Y-m-d');
    $expected_return_date = date('Y-m-d', strtotime('+7 days')); // Set to 7 days from now
    $status = 'borrowed';

    $sql = "INSERT INTO borrow (user_id, item_id, category, quantity, borrow_date, expected_return_date, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisisss", $user_id, $item_id, $category, $quantity, $borrow_date, $expected_return_date, $status);

    if ($stmt->execute()) {
        // Update the item quantity in the respective table
        $update_sql = "UPDATE " . $categories[$category] . " SET item_qty = item_qty - ? WHERE item_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $quantity, $item_id);
        $update_stmt->execute();
        $update_stmt->close();

        $borrow_status = "success";
        $borrow_message = "Item borrowed successfully!";
    } else {
        $borrow_status = "error";
        $borrow_message = "Error borrowing item: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowed Items</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
        function searchTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("equipmentTable");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let tdArray = tr[i].getElementsByTagName("td");
                let found = false;
                for (let j = 0; j < tdArray.length; j++) {
                    if (tdArray[j]) {
                        if (tdArray[j].innerText.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = found ? "" : "none";
            }
        }

        function printTable() {
            const printContents = document.getElementById("equipmentTable").outerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }

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
</head>

<style>
    div .borrow {
        transition: all 0.3s ease-in-out;
    }

    .btn:hover {
        transform: scale(1.05);
    }
</style>

<body>
    <div class="d-flex vh-100">

        <?php include '../includes/sidebar.php'; ?>

        <div class="flex-grow-1 ">
            <div class="container p-4">
                <div class="d-flex align-items-center mb-4">
                    <a href="borrow_admin.php" class="borrow btn d-flex align-items-center rounded px-4 py-2 shadow bg-info">
                        <i class="fas fa-plus me-2"></i>
                        BORROW EQUIPMENT / SUPPLIES
                    </a>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="fw-bold">LIST OF BORROWED ITEMS</h1>
                        <span class="text-gray-700">Total Borrowed Items: <?php echo $borrowed_result->num_rows; ?></span>
                        <div class="flex items-center bg-gray-700 text-white rounded-full px-4 py-2">
                            <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search Box" class="bg-gray-700 text-white outline-none">
                            <i class="fas fa-print ml-2 cursor-pointer" onclick="printTable()"></i>
                        </div>
                    </div>
                    <table id="equipmentTable" class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">Date Requested</th>
                                <th class="border border-gray-300 px-4 py-2">Name of Person</th>
                                <th class="border border-gray-300 px-4 py-2">Category</th>
                                <th class="border border-gray-300 px-4 py-2">Item / Equipment</th>
                                <th class="border border-gray-300 px-4 py-2">QTY</th>
                                <th class="border border-gray-300 px-4 py-2">Expected Return</th>
                                <th class="border border-gray-300 px-4 py-2">Status</th>
                                <th class="border border-gray-300 px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            if ($borrowed_result->num_rows > 0):
                                $row_class = 'bg-gray-100';
                                while ($row = $borrowed_result->fetch_assoc()):
                                    $row_class = $row_class === 'bg-gray-100' ? 'bg-white' : 'bg-gray-100';
                            ?>
                                    <tr class="<?php echo $row_class; ?>">
                                        <td class="border border-gray-300 px-4 py-2"><?php echo date('Y-m-d', strtotime($row['borrow_date'])); ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['user_name']); ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?php echo ucfirst(htmlspecialchars($row['category'])); ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['item_name']); ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['quantity']); ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?php echo date('Y-m-d', strtotime($row['expected_return_date'])); ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <?php if ($row['status'] !== 'returned'): ?>
                                                <a href="handle_return_admin.php?borrow_id=<?php echo $row['borrow_id']; ?>"
                                                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-2 rounded">
                                                    Return
                                                </a>
                                            <?php else: ?>
                                                <span class="text-gray-500">Returned</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php
                                endwhile;
                            else:
                                ?>
                                <tr>
                                    <td colspan="8" class="border border-gray-300 px-4 py-2 text-center">No borrowed items found.</td>
                                </tr>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<?php
$borrowed_stmt->close();
$conn->close();
?>