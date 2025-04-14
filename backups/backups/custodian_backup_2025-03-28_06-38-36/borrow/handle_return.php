<?php
session_start();
include '../config/config.php';
include '../config/conn.php';

function returnItem($conn, $borrow_id, $item_id, $category, $quantity) {
    // Update the borrow status to 'returned'
    $update_borrow_sql = "UPDATE borrow SET status = 'returned' WHERE borrow_id = ?";
    $update_borrow_stmt = $conn->prepare($update_borrow_sql);
    $update_borrow_stmt->bind_param("i", $borrow_id);

    // Update the item quantity in the respective table
    $categories = ['equipment' => 'item_equipment', 'medical' => 'medical_item', 'academic' => 'academic_item', 'cleaning' => 'cleaning_item'];
    $update_item_sql = "UPDATE " . $categories[$category] . " SET item_qty = item_qty + ? WHERE item_id = ?";
    $update_item_stmt = $conn->prepare($update_item_sql);
    $update_item_stmt->bind_param("ii", $quantity, $item_id);

    // Start transaction
    $conn->begin_transaction();

    try {
        $update_borrow_stmt->execute();
        $update_item_stmt->execute();

        // Commit transaction
        $conn->commit();

        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        return false;
    } finally {
        $update_borrow_stmt->close();
        $update_item_stmt->close();
    }
}

function handleDashboardReturn($conn, $borrow_id, $item_id, $category, $quantity) {
    if (returnItem($conn, $borrow_id, $item_id, $category, $quantity)) {
        $_SESSION['return_status'] = "success";
        $_SESSION['return_message'] = "Item returned successfully!";
    } else {
        $_SESSION['return_status'] = "error";
        $_SESSION['return_message'] = "Error returning item. Please try again.";
    }

    // Redirect back to the user dashboard
    header("Location: ../user/user_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $borrow_id = $_POST['borrow_id'];
    $item_id = $_POST['item_id'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];

    if (isset($_POST['from_dashboard'])) {
        handleDashboardReturn($conn, $borrow_id, $item_id, $category, $quantity);
    } else {
        // Original return handling (for borrow_user.php)
        if (returnItem($conn, $borrow_id, $item_id, $category, $quantity)) {
            $_SESSION['return_status'] = "success";
            $_SESSION['return_message'] = "Item returned successfully!";
        } else {
            $_SESSION['return_status'] = "error";
            $_SESSION['return_message'] = "Error returning item. Please try again.";
        }

        // Redirect back to the borrow page
        header("Location: borrow_user.php");
        exit();
    }

  
}
