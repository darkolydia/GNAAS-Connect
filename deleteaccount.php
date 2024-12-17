<?php

// Start session to get user data (e.g., user ID)
session_start(); // Start the session

include 'Lnavbar.php';
include 'sidebar.php';
include 'db_connection.php'; 

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $confirm = isset($_POST['confirm']) ? trim($_POST['confirm']) : ''; // Trim whitespace for validation
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; // Get user ID from the session

    // Check if the confirmation text is "DELETE"
    if (strtoupper($confirm) === 'DELETE') {
        // Call the deleteUserAccount function to delete the account
        $response = deleteUserAccount($conn, $userId);

        // Decode the response to display an appropriate message
        $responseDecoded = json_decode($response, true);

        // If deletion was successful, redirect to a confirmation page or show a success message
        if ($responseDecoded['success']) {
            // Optionally destroy the session or log out the user
            session_destroy(); // End the session
            header('Location: account-deleted.php'); // Redirect to a page confirming account deletion
            exit();
        } else {
            // If an error occurred, show an error message
            echo "<p class='error-message'>Error: " . htmlspecialchars($responseDecoded['message']) . "</p>";
        }
    } else {
        // If confirmation doesn't match "DELETE", show a validation message
        echo "<p class='error-message'>Error: You must type 'DELETE' to confirm.</p>";
    }
}

/**
 * Deletes a user account from the database.
 * This function relies on cascading deletes to remove related records automatically.
 *
 * @param mysqli $conn The database connection object.
 * @param string $userId The ID of the user to be deleted.
 * 
 * @return string A JSON response indicating success or failure.
 */
function deleteUserAccount($conn, $userId) {
    // SQL query to delete the user from the users table
    $deleteUserQuery = "DELETE FROM users WHERE userID = ?";

    // Prepare the query
    $stmt = $conn->prepare($deleteUserQuery);

    if (!$stmt) {
        // If preparation fails, return an error
        return json_encode([
            "success" => false,
            "message" => "Failed to prepare statement: " . $conn->error
        ]);
    }

    // Bind parameters and execute the query
    $stmt->bind_param("s", $userId);
    if ($stmt->execute()) {
        // If execution succeeds, return success response
        return json_encode([
            "success" => true,
            "message" => "User account and related records successfully deleted."
        ]);
    } else {
        // If execution fails, return an error
        return json_encode([
            "success" => false,
            "message" => "Error deleting user account: " . $stmt->error
        ]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account | Academic Clan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homepagestyle.css">
</head>
<body>
    <main class="main-content">
        <section class="section">
            <h1>Delete Account</h1>
            <p class="warning-text">
                Deleting your account will permanently erase all your data, including groups, schedules, and events. This action cannot be undone.
            </p>
            <form action="" method="post" class="delete-form">
                <label for="confirm">Type "DELETE" to confirm:</label>
                <input type="text" id="confirm" name="confirm" placeholder="DELETE" required>
                <div class="form-actions">
                    <button type="submit" class="btn btn-danger">Permanently Delete Account</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
