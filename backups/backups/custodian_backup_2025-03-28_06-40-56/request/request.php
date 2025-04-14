<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-card {
            transition: all 0.3s ease-in-out;
            text-decoration: none;
        }

        .dashboard-card:hover {
            transform: scale(1.05);
        }

        .bg-purple {
            background-color: #6f42c1;
        }

        .icon-container i {
            font-size: 2.5rem;
            padding: 0.5rem;
            border-radius: 50%;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="d-flex vh-100">
        <?php include '../includes/sidebar.php'; ?>
        <div class="flex-grow-1 p-4">
            <div class="bg-white rounded shadow p-4">
                <h1 class="text-3xl fw-bold mb-4 text-gray-800">Request</h1>
                <div class="row g-4">
                    <div class="col">
                        <a href="../equipment/add_academic.php" class="dashboard-card shadow bg-primary text-white rounded p-4 d-flex align-items-center ">
                            <i class="fas fa-book-open fs-1 me-3"></i>
                            <span class="fw-bold">Add Academic Supplies</span>
                        </a>
                    </div>
                    <div class="col">
                        <a href="../equipment/add_cleaning.php" class="dashboard-card shadow bg-success text-white rounded p-4 d-flex align-items-center">
                            <i class="fas fa-broom fs-1 me-3"></i>
                            <span class="fw-bold">Add Cleaning Supplies</span>
                        </a>
                    </div>
                    <div class="col">
                        <a href="../equipment/add_equipment.php" class="dashboard-card shadow bg-purple text-white rounded p-4  d-flex align-items-center">
                            <i class="fas fa-tools fs-1 me-3"></i>
                            <span class="fw-bold">Add New Equipment</span>
                        </a>
                    </div>
                    <div class="col">
                        <a href="../release/borrow_admin.php" class="dashboard-card shadow bg-warning text-white rounded p-4  d-flex align-items-center">
                            <i class="fas fa-hand-holding fs-1 me-3"></i>
                            <span class="fw-bold">Borrow Item/s</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>