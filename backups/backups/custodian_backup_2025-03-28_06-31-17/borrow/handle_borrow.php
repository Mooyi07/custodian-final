<?php

include '../config/config.php';
include '../config/conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process the form submission here
    $category = $_POST['category'];
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $userId = $_SESSION['userId'];
    
    // Check if the item is available in the requested quantity
    $check_sql = "SELECT item_qty FROM {$category}_item WHERE item_id = ? AND item_qty >= ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $item_id, $quantity);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Proceed with borrowing
        $borrow_date = date('Y-m-d');
        $expected_return_date = date('Y-m-d', strtotime('+7 days'));
        $status = 'pending';

        $sql = "INSERT INTO borrow (userId, item_id, category, quantity, borrow_date, expected_return_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisisss", $userId, $item_id, $category, $quantity, $borrow_date, $expected_return_date, $status);

        if ($stmt->execute()) {
            // Update the item quantity
            $update_sql = "UPDATE {$category}_item SET item_qty = item_qty - ? WHERE item_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $quantity, $item_id);
            $update_stmt->execute();

            $_SESSION['success_message'] = "Item borrowed successfully!";
        } else {
            $_SESSION['error_message'] = "Error borrowing item: " . $conn->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Requested quantity not available.";
    }

    $check_stmt->close();
    $conn->close();
}

// Redirect to the user dashboard
header("Location: ../user/user_dashboard.php");
exit();
