<?php
include '../config/config.php';
include '../config/conn.php';

function deleteCategory($categoryId) {
    global $conn;
    
    // Prepare the SQL statement
    $sql = "DELETE FROM tbl_cat WHERE cat_id = ?";
    
    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    
    // Execute the statement
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Check if a category ID was provided
if (isset($_POST['cat_id'])) {
    $categoryId = intval($_POST['cat_id']);
    
    if (deleteCategory($categoryId)) {
        // Deletion successful
        echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
    } else {
        // Deletion failed
        echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
    }
} else {
    // No category ID provided
    echo json_encode(['success' => false, 'message' => 'No category ID provided']);
}

