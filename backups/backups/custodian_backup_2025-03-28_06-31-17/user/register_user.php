<?php
include '../config/config.php';
include '../config/conn.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $role = 1; // 1 for regular user, assuming 0 is for admin or unverified users

    if ($username && $password && $confirm_password) {
        if ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $error = "Username already exists.";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $username, $password, $role);
                
                if ($stmt->execute()) {
                    $message = "Registration successful. You can now login.";
                } else {
                    $error = "Error: " . $stmt->error;
                }
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #2565AE;/* Dark background */
        }
        .register-container {
            background-color: #2d3748; /* Darker container */
            padding: 2rem; /* Padding */
            border-radius: 0.5rem; /* Rounded corners */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* Shadow */
            width: 24rem; /* Fixed width */
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="register-container">
        <h2 class="text-center text-light mb-4">User Registration</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center" role="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div class="alert alert-success text-center" role="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label text-light" for="username">Username</label>
                <input class="form-control" type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-light" for="password">Password</label>
                <input class="form-control" type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-light" for="confirm_password">Confirm Password</label>
                <input class="form-control" type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-light w-100">REGISTER</button>
            </div>
        </form>
        <a href="../login.php" class="btn btn-secondary w-100">
            <i class="fas fa-arrow-left me-2"></i>Back to User Login
        </a>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
