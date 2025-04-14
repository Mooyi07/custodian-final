<?php
include '../config/config.php';
include '../config/conn.php';

function returnItem($conn, $borrow_id) {
    // Fetch the borrow record
    $fetch_borrow_sql = "SELECT item_id, category, quantity FROM borrow WHERE borrow_id = ?";
    $fetch_borrow_stmt = $conn->prepare($fetch_borrow_sql);
    $fetch_borrow_stmt->bind_param("i", $borrow_id);
    $fetch_borrow_stmt->execute();
    $result = $fetch_borrow_stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false; // Borrow record not found
    }
    
    $borrow_data = $result->fetch_assoc();
    $item_id = $borrow_data['item_id'];
    $category = $borrow_data['category'];
    $quantity = $borrow_data['quantity'];
    
    // Update the borrow status to 'returned'
    $update_borrow_sql = "UPDATE borrow SET status = 'returned', expected_return_date = NOW() WHERE borrow_id = ?";
    $update_borrow_stmt = $conn->prepare($update_borrow_sql);

    // Check if the statement was prepared successfully
    if ($update_borrow_stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters and execute
    $update_borrow_stmt->bind_param("i", $borrow_id);
    if ($update_borrow_stmt->execute()) {
        // Update the item quantity in the respective table
        $categories = [
            'equipment' => 'item_equipment',
            'medical' => 'medical_item',
            'academic' => 'academic_item',
            'cleaning' => 'cleaning_item'
        ];
        $update_item_sql = "UPDATE " . $categories[$category] . " SET item_qty = item_qty + ? WHERE item_id = ?";
        $update_item_stmt = $conn->prepare($update_item_sql);
        $update_item_stmt->bind_param("ii", $quantity, $item_id);

        // Start transaction
        $conn->begin_transaction();

        try {
            $update_item_stmt->execute();

            // Commit transaction
            $conn->commit();

            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            return false;
        } finally {
            $fetch_borrow_stmt->close();
            $update_borrow_stmt->close();
            $update_item_stmt->close();
        }
    } else {
        return false;
    }
}

// Handle the return request
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['borrow_id'])) {
    $borrow_id = $_GET['borrow_id'];

    if (returnItem($conn, $borrow_id)) {
        $_SESSION['return_status'] = "success";
        $_SESSION['return_message'] = "Item returned successfully!";
    } else {
        $_SESSION['return_status'] = "error";
        $_SESSION['return_message'] = "Error returning item. Please try again.";
    }

    // Redirect back to the release page
    header("Location: release.php");
    exit();
}

$conn->close();
?>
