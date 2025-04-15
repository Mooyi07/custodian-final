<!DOCTYPE html>
<html lang="eng">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            background-color: #424242;
            width: 230px;
            transition: background-color 0.3s;
        }

        .sidebar a {
            text-decoration: none;
            transition: all 0.3s ease-in-out;
            font-weight: 500;
            letter-spacing: 0.5px;
            font-size: 16px;
            color: #f0f0f0;
        }

        .sidebar a:hover {
            background-color: hsl(0, 0.00%, 50%);
            color: #ffffff;
            transform: scale(1.05);
        }
        @media print {
            .no-print {
                display: none!important;
            }
            .main-content {
                width: 100%;
            }
            .print-header{
                display: block!important;
            }
        }

        .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        padding: 15px;
        overflow-y: auto;
        }
        .content {
        margin-left: 200px; /* Ensure content is not covered */
        padding: 20px;
    }
    </style>
    
</head>

<body>
    <div class="d-flex vh-100 no-print">
        <div class="sidebar p-3 d-flex flex-column">
            <a href="../index.php" class="btn text-start mb-3 w-100">
                <i class="fas fa-tachometer-alt me-2"></i>
                Dashboard
            </a>
            <a href="../stocks/academic.php" class="btn text-start mb-3 w-100">
                <i class="fas fa-boxes me-2"></i>
                Stock Availability
            </a>
            <a href="../inventory/inventory.php" class="btn text-start mb-3 w-100">
                <i class="fas fa-clipboard-list me-2"></i>
                Inventory
            </a>
            <a href="../release/release.php" class="btn text-start mb-3 w-100">
                <i class="fas fa-dolly me-2"></i>
                Releasing
            </a>
            <a href="../report/report.php" class="btn text-start mb-3 w-100">
                <i class="fas fa-pencil me-2"></i>
                Report
            </a>
            <a href="../request/request.php" class="btn text-start mb-3 w-100">
                <i class="fas fa-file me-2"></i>
                Request
            </a>
            <a href="../approval/approval.php" class="btn text-start mb-3 w-100">
                <i class="fas fa-check-circle me-2"></i>
                Approval
            </a>
            <a href="../user/user_management.php" class="btn text-start mb-3 w-100">
                <i class="fas fa-users me-2"></i>
                User Management
            </a>
            <a href="../backups/backup.php" class="btn text-start mb-3 w-100">
                <i class="fa-regular fa-folder-closed"></i>
                Backup
            </a>
            <div class="mt-auto">
                <a href="../logout.php" class="btn text-start w-100">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
                <i class=""></i>
            </div>
        </div>
    </div>

    <main class="content">

    </main>

</body>


</html>