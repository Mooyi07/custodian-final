<?php
require_once('./config/config.php');
require_once('./config/conn.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Function to set session variables based on user role
function setSessionForRole($role, $user)
{
    $_SESSION['isLoggedIn'] = true;
    $_SESSION['userId'] = $user['userId']; // Set user ID in session
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
                        header('Location: index.php'); // Redirect to admin dashboard
                    } else {
                        header('Location: /custodian/user/user_dashboard.php'); // Redirect to user dashboard
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
    <!-- TailwindCSS CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!-- Fontawesome icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Login - Invensure</title>

    <!-- <style>
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
    </style> -->
</head>

<body class="bg-[url('img/bg.jpg')] bg-cover bg-center bg-no-repeat w-full h-screen">

    <div class="flex items-center justify-center bg-[url('img/san_ramon.jpg')] bg-cover bg-center bg-no-repeat w-full h-[45vh]">
        <div class="absolute inset-0 bg-black opacity-40"></div>
        <div class="relative top-52 bg-white/30 backdrop-blur-sm border border-white/10 p-8 rounded-lg shadow-xl max-w-sm w-full">
            <h2 class="text-3xl font-bold ext-center text-black mb-6 text-center">Login</h2>

            <form action="" method="POST" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-bold text-black">Username</label>
                    <input type="text" id="username" name="username" class="w-full p-3 mt-1 bg-transparent border border-black/50 rounded-md focus:outline-none" placeholder="Enter Username">
                </div>

                <script>
                    function toggleVisibility() {
                        const password = document.getElementById('password');
                        const icon = document.querySelector(".password-toggle-icon");
                        password.type = password.type === "password" ? "text" : "password";
                        icon.classList.toggle("fa-eye-slash");
                        icon.classList.toggle("fa-eye");
                    }
                </script>

                <div class="relative">
                    <label for="password" class="block text-sm font-bold text-black">Password</label>
                    <input type="password" id="password" name="password" class="w-full p-3 mt-1 bg-transparent border border-black/50 rounded-md focus:outline-none pr-10" placeholder="Enter Password">

                    <i onclick="toggleVisibility()" class="password-toggle-icon fa-regular fa-eye-slash absolute right-3 top-[50px] transform -translate-y-1/2 text-black cursor-pointer"></i>
                </div>


                <div>
                    <button type="submit" class="w-full py-3 px-6 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                        Login
                    </button>
                </div>
            </form>


           
        </div>
        <!-- <div class="text-white py-16 relative -bottom-44">
            <div class="shadow-xl p-8 w-[450px] mx-auto rounded-lg bg-blue-800/70">
                <h1 class="font-bold text-3xl mb-6 text-center text-white">Login</h1>
                <form action="" method="POST">
                    <div class="mb-6">
                        <label for="username" class="font-bold block mb-2 text-white">Username</label>
                        <input type="text" class="w-full p-3 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-blue-200/50 placeholder-blue-500" id="username" name="username" placeholder="Enter username" required>
                    </div>

                    <div class="mb-6">
                        <label for="password" class="font-bold block mb-2 text-white">Password</label>
                        <input type="password" class="w-full p-3 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-blue-200/50 placeholder-blue-500" id="password" name="password" placeholder="Enter password" required>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-full mt-4 focus:ring-2 focus:ring-blue-500 focus:outline-none transition duration-300 ease-in-out">LOGIN</button>
                </form>
            </div>
        </div> -->
    </div>





    <!-- <div class="login-container shadow">
        <h2 class="login-title">LOGIN</h2>

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
    </div> -->

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>