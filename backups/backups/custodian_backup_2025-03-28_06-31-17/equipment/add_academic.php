<?php
include '../config/config.php';
include '../config/conn.php';

if (!isset($_SESSION['isLoggedIn'])) {
    header('location: login.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['new_unit'])) {
        $new_unit = $_POST['new_unit'];
        $insert_sql = "INSERT INTO units (unit_name) VALUES (?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("s", $new_unit);
        if ($stmt->execute()) {
            echo "Unit added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch units from the database
$units_sql = "SELECT unit_name FROM units";
$units_result = $conn->query($units_sql);

if ($units_result === false) {
    die('Query failed: ' . htmlspecialchars($conn->error));
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $item_name = $_POST['item_name'];
    $item_code = 0;
    $item_qty = $_POST['item_qty'];
    $item_unit = $_POST['item_unit'];
    $item_brand = $_POST['item_brand'];
    $date_purchase = $_POST['date_purchase'];
    $date_added = $_POST['date_added'];
    $item_location = $_POST['item_location'];
    $item_life = $_POST['item_life'];
    $item_remarks = $_POST['item_remarks'];
    $equipment_type = $_POST['equipment_type'];

    // First, check if the item already exists
    $check_sql = "SELECT item_id FROM academic_item WHERE item_name = ? AND item_unit = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $item_name, $item_unit);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Item exists, add to inventory history
        $row = $check_result->fetch_assoc();
        $item_id = $row['item_id'];
        
        $history_sql = "INSERT INTO inventory_history (item_id, item_name, quantity_added, item_unit, date_added, added_by) VALUES (?, ?, ?, ?, ?, ?)";
        $history_stmt = $conn->prepare($history_sql);
        $user_id = $_SESSION['userId'];
        $history_stmt->bind_param("isissi", $item_id, $item_name, $item_qty, $item_unit, $date_added, $user_id);
        
        if ($history_stmt->execute()) {
            $_SESSION['success_message'] = "Inventory updated successfully!";
            header("Location: ../stocks/academic.php");
            exit();
        }
    } else {
        // Item doesn't exist, create new item
        $sql = "INSERT INTO `academic_item`(`item_name`, `item_code`, `item_qty`, `item_unit`, `item_brand`, `date_purchase`, `item_location`, `item_life`, `item_remarks`, `equipment_type`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisssssss", $item_name, $item_code, $item_qty, $item_unit, $item_brand, $date_purchase, $item_location, $item_life, $item_remarks, $equipment_type);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Academic item added successfully!";
        header("Location: ../stocks/academic.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }
    $stmt->close();
    }
}
?>
 

<?php
// Get item details from URL parameters
$item_name = isset($_GET['item_name']) ? htmlspecialchars($_GET['item_name']) : '';
$item_unit = isset($_GET['item_unit']) ? htmlspecialchars($_GET['item_unit']) : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Academic Item</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body, html {
            height: 100%; /* Ensure the body and html take full height */
            margin: 0; /* Remove default margin */
        }
        .form-container {
            height: 100vh; /* Full height of the viewport */
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center content vertically */
            overflow-y: auto; /* Allow scrolling if content overflows */
            padding: 20px; /* Add some padding */
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
                <h1 class="text-3xl fw-bold mb-3 text-center">Add New Academic Item</h1>

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
                                <label for="item_name" class="form-label fw-bold">Academic Item</label>
                                <input type="text" class="form-control" id="item_name" name="item_name" placeholder="Enter Academic Item" value="<?php echo $item_name; ?>" required>
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
                                    <?php
                                    $units = ['pcs', 'liters', 'meters', 'ream', 'gallons', 'pounds', 'tons', 'boxes', 'packs', 'rolls', 'cases', 'bundles', 'sets'];
                                    foreach ($units as $unit) {
                                        $selected = ($unit === $item_unit) ? 'selected' : '';
                                        echo "<option value=\"$unit\" $selected>" . ucfirst($unit) . "</option>";
                                    }
                                    ?>
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
                                <label for="equipment_type" class="form-label fw-bold">Equipment Type</label>
                                <select class="form-select" id="equipment_type" name="equipment_type" required>
                                    <option selected disabled value="">Select Equipment Type</option>
                                    <option value="Equipment">Equipment</option>
                                    <option value="Supply">Supply</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="date_added" class="form-label fw-bold">Date Added</label>
                                <input type="date" class="form-control" id="date_added" name="date_added" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="date_purchase" class="form-label fw-bold">Purchase Date</label>
                                <input type="date" class="form-control" id="date_purchase" name="date_purchase" required>
                            </div>
                            <div class="col-md-6">
                                <label for="item_brand" class="form-label fw-bold">Brand</label>
                                <input type="text" class="form-control" id="item_brand" name="item_brand" placeholder="Enter Brand" required>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="item_location" class="form-label fw-bold">Location</label>
                                <input type="text" class="form-control" id="item_location" name="item_location" placeholder="Enter Location" required>
                            </div>
                            <div class="col-md-6">
                                <label for="item_life" class="form-label fw-bold">Expiration</label>
                                <input type="text" class="form-control" id="item_life" name="item_life">
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="item_remarks" class="form-label fw-bold">Remarks</label>
                                <textarea class="form-control" id="item_remarks" name="item_remarks" rows="2" placeholder="Enter Remarks"></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary fw-medium">
                                Add Academic Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addUnit() {
            let unitInput = document.getElementById("newUnit");
            let unitSelect = document.getElementById("unitSelect");
            let unitValue = unitInput.value.trim();

            if (unitValue === "") {
                alert("Please enter a unit!");
                return;
            }

            // Check if the unit already exists in the dropdown
            for (let i = 0; i < unitSelect.options.length; i++) {
                if (unitSelect.options[i].value.toLowerCase() === unitValue.toLowerCase()) {
                    alert("This unit already exists!");
                    return;
                }
            }

            // Create new option element
            let newOption = document.createElement("option");
            newOption.value = unitValue;
            newOption.textContent = unitValue;

            // Append to the select dropdown
            unitSelect.appendChild(newOption);

            // Select the newly added unit
            unitSelect.value = unitValue;

            // Clear input field
            unitInput.value = "";
        }
    </script>
</body>

</html>