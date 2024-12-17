<?php
include 'navbar.php';
include 'db_connection.php'; // Database connection

session_start(); // Start the session

$error_message = ''; // Error message for login failure

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the necessary POST fields are set
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Query to find the user with the given email
        $query = "SELECT userID, firstName, lastName, passwordHash FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a user was found with this email
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['passwordHash'])) {
                // Password is correct, store the user data in session
                $_SESSION['user_id'] = $user['userID'];
                $_SESSION['user_name'] = $user['firstName'] . ' ' . $user['lastName'];

                // Redirect to homepage after successful login
                header("Location: homepage.php");
                exit(); // Stop script execution
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "Email not found.";
        }
    } else {
        $error_message = "Please enter both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GNAAS Connect</title>
    <link rel="stylesheet" href="homepagestyle.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
        }
        .form-page {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .input-group input:focus {
            border-color: #007bff;
            outline: none;
        }
        .options {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .form-footer {
            text-align: center;
            font-size: 14px;
        }
        .form-footer a {
            color: #007bff;
            text-decoration: none;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <main class="form-page">
        <div class="form-container">
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form id="loginForm" action="login.php" method="post">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your Ashesi email" required>
                    <span id="email-error" class="error-message" style="display:none;"></span>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span id="password-error" class="error-message" style="display:none;"></span>
                </div>
                <div class="options">
                    <label><input type="checkbox"> Remember Me</label>
                    <a href="reset-password.php">Forgot password?</a>
                </div>
                <button type="submit" class="btn">Login</button>
                <p class="form-footer">Don't have an account? <a href="signup.php">Register here</a></p>
            </form>
        </div>
    </main>
</body>
</html>
