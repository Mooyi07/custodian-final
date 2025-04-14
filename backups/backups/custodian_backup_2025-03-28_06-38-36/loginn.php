<?php 
require_once(__DIR__ . '/config/config.php'); 
require_once(__DIR__ . '/config/conn.php');  

// Enable error reporting for debugging
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL); 

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Function to set session variables based on user role
function setSessionForRole($role, $user) {
    $sessionKey = ($role == 1) ? 'users' : 'admin';  // Define session key
    $_SESSION[$sessionKey] = [
        'userId' => $user['userId'],
        'username' => $user['username'],
        'role' => $user['role'],
        'isLoggedIn' => true
    ];
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
                    header('Location: index.php');
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

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        body {
            background-color: #2565AE;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
        }
        .background-image {
            background-image: url('img/san_ramon.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 45vh;
            width: 100%;
        }
        .login-container {
            background-color: rgba(173, 216, 230, 0.8);
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin-top: -4rem;
        }
        .login-title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #000;
            margin-bottom: 1.5rem;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.5);
        }
        .btn-login {
            background-color: #1a202c;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .btn-login:hover {
            background-color: #2d3748;
        }
        .register-link {
            color: #000;
            text-decoration: none;
        }
        .register-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Background Image -->
    <div class="background-image"></div>

    <!-- Login Form -->
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
                <label for="username" class="form-label fw-bold">Username</label>
                <input type="text" class="form-control rounded-pill" id="username" name="username" placeholder="Enter username" required>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-bold">Password</label>
                <input type="password" class="form-control rounded-pill" id="password" name="password" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn btn-login w-100 rounded-pill">LOGIN</button>
        </form>

        <div class="mt-3 text-center">
            <a href="userportal/register_user.php" class="register-link">Register New User</a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
