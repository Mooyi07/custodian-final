<?php 
include("C:/xampp/htdocs/custodian/config/config.php");
include("C:/xampp/htdocs/custodian/config/conn.php");

if (!isset($_SESSION['isLoggedIn'])) {
    header('location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #2565AE; /* Light blue background */
            color: #333; /* Darker text for contrast */
        }
        .dashboard-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        .dashboard-card {
            background-color: #b3e5fc; /* Light blue card background */
            color: #01579b; /* Darker blue text for contrast */
            padding: 2rem;
            border-radius: 0.5rem;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            background-color: #81d4fa; /* Slightly darker blue on hover */
        }
        .card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #01579b; /* Darker blue for icons */
        }
        @media (max-width: 768px) {
            .dashboard-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

    <!-- Logout Button -->
    <div class="position-absolute top-0 end-0 p-3">
        <form method="post">
            <button type="submit" name="logout" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>

    <!-- Dashboard Cards -->
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-5">
                <a href="stocks/academic.php" class="dashboard-card d-block text-decoration-none">
                    <i class="fas fa-boxes card-icon"></i>
                    <h2 class="h5 fw-bold">AVAILABILITY OF STOCK</h2>
                    <p class="small text-muted">Check current stock levels</p>
                </a>
            </div>

            <div class="col-md-6 col-lg-5">
                <a href="inventory/inventory.php" class="dashboard-card d-block text-decoration-none">
                    <i class="fas fa-clipboard-list card-icon"></i>
                    <h2 class="h5 fw-bold">INVENTORY REPORT</h2>
                    <p class="small text-muted">View detailed inventory information</p>
                </a>
            </div>

            <div class="col-md-6 col-lg-5">
                <a href="./release/release.php" class="dashboard-card d-block text-decoration-none">
                    <i class="fas fa-dolly card-icon"></i>
                    <h2 class="h5 fw-bold">RELEASED EQUIPMENT / SUPPLIES</h2>
                    <p class="small text-muted">Track released items</p>
                </a>
            </div>

            <div class="col-md-6 col-lg-5">
                <a href="../custodian/request/request.php" class="dashboard-card d-block text-decoration-none">
                    <i class="fas fa-file-alt card-icon"></i>
                    <h2 class="h5 fw-bold">REQUEST</h2>
                    <p class="small text-muted">Manage equipment and supply requests</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
