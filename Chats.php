<?php
include 'Lnavbar.php';
include 'sidebar.php';
include 'db_connection.php'; // Database connection

session_start(); // Start the session

// Get the current user ID from the session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch all users who are friends with the logged-in user (status = 'accepted')
$query = "
    SELECT u.userId, u.firstName, u.lastName, u.bio
    FROM users u
    JOIN friendships f ON (u.userId = f.userId1 OR u.userId = f.userId2)
    WHERE f.status = 'accepted' 
    AND (f.userId1 = ? OR f.userId2 = ?)
    AND u.userId != ?;  -- Exclude the logged-in user from the result
";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $user_id, $user_id, $user_id); // Bind the userId for preventing showing the logged-in user
$stmt->execute();
$user_result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chats | GNAAS Connect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homepagestyle.css">
</head>
<body>
    <main class="main-content">
        <section class="section">
            <h1>Chats </h1>
            
            <div class="groups-container">
                <?php if ($user_result->num_rows > 0): ?>
                    <!-- Display users who are friends -->
                    <?php while ($user = $user_result->fetch_assoc()): ?>
                        <div class="group-item">
                            <h2><?php echo htmlspecialchars($user['firstName']) . ' ' . htmlspecialchars($user['lastName']); ?></h2>
                            <p><strong>Bio:</strong> <?php echo htmlspecialchars($user['bio']); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No friends found.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
