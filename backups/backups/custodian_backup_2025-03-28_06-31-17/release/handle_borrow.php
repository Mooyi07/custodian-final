<?php
session_start();
include '../config/config.php';
include '../config/conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'] ?? '';
    $item_id = $_POST['item_id'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $user_id = $_POST['user_id'] ?? ''; // Make sure you're passing user_id from the form

    // Validate inputs
    if (empty($category) || empty($item_id) || empty($quantity) || empty($user_id)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: borrow_admin.php");
        exit();
    }

    // Add your logic to insert the borrow record into the database
    $borrow_date = date('Y-m-d');
    $expected_return_date = date('Y-m-d', strtotime('+7 days')); // Set to 7 days from now
    $status = 'borrowed';

    $sql = "INSERT INTO borrow (user_id, item_id, category, quantity, borrow_date, expected_return_date, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisisss", $user_id, $item_id, $category, $quantity, $borrow_date, $expected_return_date, $status);

    try {
        if ($stmt->execute()) {
            // Update the item quantity in the respective table
            $categories = [
                'equipment' => 'item_equipment',
                'medical' => 'medical_item',
                'academic' => 'academic_item',
                'cleaning' => 'cleaning_item'
            ];
            $update_sql = "UPDATE " . $categories[$category] . " SET item_qty = item_qty - ? WHERE item_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $quantity, $item_id);
            $update_stmt->execute();
            $update_stmt->close();

            $_SESSION['success'] = "Item borrowed successfully!";
        } else {
            $_SESSION['error'] = "Error borrowing item: " . $conn->error;
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid request method.";
}

$conn->close();
header("Location: release.php");
exit();
