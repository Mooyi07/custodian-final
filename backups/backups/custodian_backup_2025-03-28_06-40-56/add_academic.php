<?php
include '../config/config.php';
include '../config/conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $item_qty = $_POST['item_qty'];
    $item_unit = $_POST['item_unit'];
    $item_brand = $_POST['item_brand'];
    $date_purchase = $_POST['date_purchase'];
    $item_location = $_POST['item_location'];
    $item_life = $_POST['item_life'];
    $item_remarks = $_POST['item_remarks'];

    // Insert the new academic supply into the database
    $sql = "INSERT INTO academic_item (item_name, item_qty, item_unit, item_brand, date_purchase, item_location, item_life, item_remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssss", $item_name, $item_qty, $item_unit, $item_brand, $date_purchase, $item_location, $item_life, $item_remarks);

    if ($stmt->execute()) {
        echo "<script>alert('Academic supply added successfully!'); window.location.href='academic.php';</script>";
    } else {
        echo "<script>alert('Error adding supply: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Academic Supply</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Add Academic Supply</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="item_name" class="form-label">Item Name</label>
                <input type="text" name="item_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="item_qty" class="form-label">Quantity</label>
                <input type="number" name="item_qty" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="item_unit" class="form-label">Unit</label>
                <input type="text" name="item_unit" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="item_brand" class="form-label">Brand</label>
                <input type="text" name="item_brand" class="form-control">
            </div>
            <div class="mb-3">
                <label for="date_purchase" class="form-label">Date of Purchase</label>
                <input type="date" name="date_purchase" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="item_location" class="form-label">Location</label>
                <input type="text" name="item_location" class="form-control">
            </div>
            <div class="mb-3">
                <label for="item_life" class="form-label">Life Span</label>
                <input type="text" name="item_life" class="form-control">
            </div>
            <div class="mb-3">
                <label for="item_remarks" class="form-label">Remarks</label>
                <textarea name="item_remarks" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Supply</button>
            <a href="academic.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?> 