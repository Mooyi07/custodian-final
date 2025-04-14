<?php
require_once(__DIR__ . '../config/config.php');
require_once(__DIR__ . '../config/conn.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to set session variables based on user role
function setSessionForRole($role, $user) {
    $_SESSION['isLoggedIn'] = true;
    $_SESSION['user_id'] = $user['userId']; // Set user ID in session
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role']; // Store user role in session
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($username) && !empty($password)) {
        // Prepare SQL query
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Direct password comparison (No hashing)
                if ($password == $user['password']) {
                    setSessionForRole($user['role'], $user);
                    
                    // Redirect based on user role
                    if ($user['role'] == 0) { // Assuming 0 is for admin
                        header('Location: admin_dashboard.php'); // Redirect to admin dashboard
                    } else {
                        header('Location: user_dashboard.php'); // Redirect to user dashboard
                    }
                    exit();
                } else {
                    $error = "Invalid username or password";
                }
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Database query failed: " . $conn->error;
        }
    } else {
        $error = "Please enter both username and password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - San Ramon Catholic School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Light background for the login page */
        }
        .login-container {
            max-width: 400px;
            margin: auto;
            margin-top: 100px; /* Center the form vertically */
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.9); /* Slightly transparent white */
            border-radius: 0.5rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-title {
            text-align: center;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

    <div class="login-container shadow">
        <h2 class="login-title">LOGIN</h2>

        <!-- Display Error Message -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">LOGIN</button>
        </form>

        <div class="mt-3 text-center">
            <a href="userportal/register_user.php" class="text-decoration-none">Register New User</a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
