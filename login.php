<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'Lnavbar.php';
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

                // Debugging: Check if session variables are correctly set
                // echo "User ID: " . $_SESSION['user_id'];  // Uncomment for debugging

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
</head>
<body>
    <main class="form-page">
        <div class="form-container">
            <?php if (!empty($error_message)): ?>
                <p class="error-message" style="color:red;"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form id="loginForm" action="login.php" method="post">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your Ashesi email" required>
                    <span id="email-error" class="error-message" style="display:none; color:red;"></span>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span id="password-error" class="error-message" style="display:none; color:red;"></span>
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



