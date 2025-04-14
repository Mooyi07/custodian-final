<?php

  require_once('config/conn.php');

  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // declare and store values from form Data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $Checkquery = "SELECT * FROM users WHERE username = '$username'";
    $query = $conn->query($Checkquery);
    if ($query->num_rows > 0)
    {
      echo "Username already exist"; 
    }
    else
    {
      $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
      $result = $conn->query($sql);
      if ($result) {    
        header('location: login.php');
      }else{
        echo "There was a problem in registering your account!";
      }     
    }
  } 

?>
<!doctype html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #1a202c; /* bg-gray-900 */
        }
        .login-container {
            background-color: #2d3748; /* bg-gray-800 */
            padding: 2rem; /* p-8 */
            border-radius: 0.5rem; /* rounded-lg */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* shadow-lg */
            width: 24rem; /* w-96 */
        }
        .login-title {
            text-align: center; /* text-center */
            font-size: 1.5rem; /* text-2xl */
            font-weight: 600; /* font-semibold */
            color: #e2e8f0; /* text-gray-200 */
            margin-bottom: 1.5rem; /* mb-6 */
        }
        .form-label {
            display: block; /* block */
            color: #a0aec0; /* text-gray-400 */
            margin-bottom: 0.5rem; /* mb-2 */
        }
        .form-input {
            width: 100%; /* w-full */
            padding: 0.5rem 1rem; /* px-4 py-2 */
            background-color: #4a5568; /* bg-gray-700 */
            color: #cbd5e0; /* text-gray-300 */
            border-radius: 9999px; /* rounded-full */
            outline: none; /* focus:outline-none */
            box-shadow: 0 0 0 2px transparent; /* focus:ring-2 */
            transition: box-shadow 0.2s; /* focus:ring-gray-600 */
        }
        .form-input:focus {
            box-shadow: 0 0 0 2px #2d3748; /* focus:ring-gray-600 */
        }
        .login-button {
            width: 100%; /* w-full */
            padding: 0.5rem; /* py-2 */
            background-color: #1a202c; /* bg-gray-900 */
            color: #e2e8f0; /* text-gray-200 */
            border-radius: 9999px; /* rounded-full */
            transition: background-color 0.2s; /* hover:bg-gray-700 */
            outline: none; /* focus:outline-none */
            box-shadow: 0 0 0 2px transparent; /* focus:ring-2 */
        }
        .login-button:hover {
            background-color: #2d3748; /* hover:bg-gray-700 */
        }
        .login-button:focus {
            box-shadow: 0 0 0 2px #2d3748; /* focus:ring-gray-600 */
        }
        .forgot-password {
            text-align: center; /* text-center */
            color: #f56565; /* text-red-500 */
            transition: color 0.2s; /* hover:underline */
        }
        .forgot-password:hover {
            text-decoration: underline; /* hover:underline */
        }
        .forgot-password span {
            color: #e53e3e; /* text-red-600 */
        }
    </style>
    </head>
  <body>
    
    <div class="flex items-center justify-center min-h-screen">
      <div class="login-container">
      <h2 class="login-title">LOGIN</h2>
      <form action="" method="POST">
            
              <form action="" method="POST">
                <!-- username -->
                <div class="mb-4">
                 <label class="form-label" for="username">Username</label>
                 <input class="form-input" type="text" id="username" name="username" >
        </div>
        <div class="mb-6">
                 <label class="form-label" for="password">Password</label>
                 <input class="form-input" type="password" id="password" name="password">
        </div>
                <button type="submit" class="btn btn-success">Create Account</button>

                <a href="login.php">Login your Account</a>

              </form>
              <?php if (isset($msg)): ?>
                <div class="alert alert-danger mt-3"><?php echo $msg ?></div>
              <?php endif ?>
        
          </div>
        </div>
      </div>
    </div> 
  </body>
</html>