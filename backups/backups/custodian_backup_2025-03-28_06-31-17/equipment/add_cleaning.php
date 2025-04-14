<?php
include '../config/config.php';
include '../config/conn.php';

if (!isset($_SESSION['isLoggedIn'])) {
    header('location: login.php');
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $item_name = $_POST['item_name'];
    $item_code = 0;
    $item_qty = $_POST['item_qty'];
    $item_unit = $_POST['item_unit'];
    $item_brand = $_POST['item_brand'];
    $date_purchase = $_POST['date_purchase'];
    $item_location = $_POST['item_location'];
    $item_life = $_POST['item_life'];
    $item_remarks = $_POST['item_remarks'];

    $sql = "INSERT INTO `cleaning_item`(`item_name`, `item_code`, `item_qty`, `item_unit`, `item_brand`, `date_purchase`, `item_location`, `item_life`, `item_remarks`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siissssss", $item_name, $item_code, $item_qty, $item_unit, $item_brand, $date_purchase, $item_location, $item_life, $item_remarks);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Cleaning item added successfully!";
        header("Location: ../stocks/cleaning.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Cleaning Item</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
        .form-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
            padding: 20px;
        }
        .form-control, .form-select {
            padding: 0.5rem;
        }
        .btn-primary {
            padding: 0.5rem 1rem;
        }
    </style>
</head>

<body class="bg-body-secondary">
    <div class="d-flex h-100">
        <?php include '../includes/sidebar.php'; ?>
        <div class="flex-grow-1">
            <div class="container form-container">
                <h1 class="text-3xl fw-bold mb-3 text-center">Add New Cleaning Item</h1>

                <?php
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-3" role="alert">';
                    echo '<p class="fw-bold">Error</p>';
                    echo '<p>' . $_SESSION['error_message'] . '</p>';
                    echo '</div>';
                    unset($_SESSION['error_message']);
                }
                ?>

                <div class="bg-white shadow rounded px-4 pt-4 pb-4 mb-3">
                    <form action="" method="POST">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="item_name" class="form-label fw-bold">Cleaning Item</label>
                                <input type="text" class="form-control" id="item_name" name="item_name" placeholder="Enter Cleaning Item" required>
                            </div>
                            <div class="col-md-6">
                                <label for="item_qty" class="form-label fw-bold">Quantity</label>
                                <input type="number" class="form-control" id="item_qty" name="item_qty" placeholder="Enter Quantity" required>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="item_unit" class="form-label fw-bold">Unit</label>
                                <select id="item_unit" name="item_unit" class="form-select" required>
                                    <option value="">Select Unit</option>
                                    <option value="pcs">Pcs</option>
                                    <option value="liters">Liters</option>
                                    <option value="meters">Meters</option>
                                    <option value="ream">Ream</option>
                                    <option value="gallons">Gallons</option>
                                    <option value="pounds">Pounds</option>
                                    <option value="tons">Tons</option>
                                    <option value="boxes">Boxes</option>
                                    <option value="packs">Packs</option>
                                    <option value="rolls">Rolls</option>
                                    <option value="cases">Cases</option>
                                    <option value="bundles">Bundles</option>
                                    <option value="sets">Sets</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="item_brand" class="form-label fw-bold">Brand</label>
                                <input type="text" class="form-control" id="item_brand" name="item_brand" placeholder="Enter Brand" required>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="date_purchase" class="form-label fw-bold">Purchase Date</label>
                                <input type="date" class="form-control" id="date_purchase" name="date_purchase" required>
                            </div>
                            <div class="col-md-6">
                                <label for="item_location" class="form-label fw-bold">Location</label>
                                <input type="text" class="form-control" id="item_location" name="item_location" placeholder="Enter Location" required>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="item_life" class="form-label fw-bold">Expiration</label>
                                <input type="text" class="form-control" id="item_life" name="item_life">
                            </div>
                            <div class="col-md-6">
                                <label for="item_remarks" class="form-label fw-bold">Remarks</label>
                                <textarea class="form-control" id="item_remarks" name="item_remarks" rows="2" placeholder="Enter Remarks"></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary fw-medium">
                                Add Cleaning Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
