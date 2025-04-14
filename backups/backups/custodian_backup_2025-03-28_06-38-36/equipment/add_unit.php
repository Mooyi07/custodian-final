<?php
include '../config/config.php';
include '../config/conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_unit = $_POST['new_unit'];
    if (!empty($new_unit)) {
        $insert_sql = "INSERT INTO units (unit_name) VALUES (?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("s", $new_unit);
        if ($stmt->execute()) {
            echo "Unit added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        header("Location: add_academic.php");
        exit();
    } else {
        echo "Please enter a unit name.";
    }
}
?>

