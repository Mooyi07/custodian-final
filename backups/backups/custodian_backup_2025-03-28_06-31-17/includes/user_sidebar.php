<div class="sidebar bg-dark text-white p-4" style="width: 250px; height: 100vh; position: fixed; border-radius: 15px;">
    <h2 class="h4 text-center mb-4">User Dashboard</h2>
    <nav class="mt-4">
        <a class="nav-link text-white" href="../user/user_dashboard.php">
            <i class="fas fa-home"></i> Home
        </a>
        <a class="nav-link text-white" href="../request/request_user.php">
            <i class="fas fa-clipboard-list"></i> My Requests
        </a>
        <a class="nav-link text-white" href="../user/account_settings.php">
            <i class="fas fa-user-cog"></i> Profile Settings
        </a>
    </nav>
    <div class="mt-auto">
        <a href="logout.php" class="nav-link text-white">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<style>
    .sidebar {
        background-color: #343a40; /* Dark background for the sidebar */
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5); /* Add shadow for depth */
        border-radius: 15px; /* Rounded corners for the sidebar */
    }
    .nav-link {
        transition: background-color 0.3s; /* Smooth transition for hover effect */
        padding: 10px 15px; /* Add padding for better click area */
    }
    .nav-link:hover {
        background-color: #495057; /* Darker background on hover */
        border-radius: 5px; /* Rounded corners on hover */
    }
    .sidebar h2 {
        font-weight: bold; /* Bold title */
        text-align: center; /* Center the title */
    }
</style>
