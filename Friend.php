<?php
include 'navbar.php';
include 'sidebar.php';
include 'db_connection.php'; // Database connection

session_start(); // Start the session

// Get the current user ID from the session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch all users except the logged-in user and those with pending or accepted friend requests
$query = "
    SELECT u.userId, u.firstName, u.lastName, u.bio
    FROM users u
    LEFT JOIN friendships f ON (f.userId1 = u.userId AND f.userId2 = ?) OR (f.userId1 = ? AND f.userId2 = u.userId)
    WHERE u.userId != ? AND f.userId1 IS NULL
";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $user_id, $user_id, $user_id); // Prevent showing the logged-in user
$stmt->execute();
$user_result = $stmt->get_result();

// Fetch all received friend requests (requests where the logged-in user is userId2)
$request_query = "
    SELECT u.userId, u.firstName, u.lastName, u.bio, f.userId1 AS requester_id, f.status, f.dateCreated
    FROM users u
    JOIN friendships f ON f.userId1 = u.userId
    WHERE f.userId2 = ? AND f.status = 'pending'
";
$request_stmt = $conn->prepare($request_query);
$request_stmt->bind_param("s", $user_id); // Fetch requests sent to the logged-in user
$request_stmt->execute();
$request_result = $request_stmt->get_result();

// Handle Send Friend Request functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Send Friend Request
    if (isset($_POST['receiver_user_id'])) {
        $receiver_id = $_POST['receiver_user_id'];

        // Ensure the sender is not sending a friend request to themselves
        if ($receiver_id != $user_id) {
            // Check if a friend request already exists between the users
            $check_query = "
                SELECT * FROM friendships 
                WHERE (userId1 = ? AND userId2 = ?) OR (userId1 = ? AND userId2 = ?)
            ";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("ssss", $user_id, $receiver_id, $receiver_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Existing request (pending, accepted, or rejected)
                $existing_request = $result->fetch_assoc();
                if ($existing_request['status'] == 'pending') {
                    echo "<p class='error-message'>Friend request already sent.</p>";
                } elseif ($existing_request['status'] == 'accepted') {
                    echo "<p class='success-message'>You are already friends.</p>";
                } else {
                    echo "<p class='error-message'>You have previously rejected each other's requests.</p>";
                }
            } else {
                // Insert new pending friend request
                $insert_query = "INSERT INTO friendships (userId1, userId2, status) VALUES (?, ?, 'pending')";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("ss", $user_id, $receiver_id);

                if ($stmt->execute()) {
                    echo "<p class='success-message'>Friend request sent!</p>";
                } else {
                    echo "<p class='error-message'>Error sending friend request: " . $stmt->error . "</p>";
                }
            }
        } else {
            echo "<p class='error-message'>You cannot send a friend request to yourself.</p>";
        }
    }

    // Handle Accept Friend Request functionality
    if (isset($_POST['requester_id'])) {
        $requester_id = $_POST['requester_id'];

        // Update the friendship status to 'accepted'
        $accept_query = "
            UPDATE friendships
            SET status = 'accepted', dateAccepted = NOW()
            WHERE (userId1 = ? AND userId2 = ?) OR (userId1 = ? AND userId2 = ?)
        ";
        $stmt = $conn->prepare($accept_query);
        $stmt->bind_param("ssss", $requester_id, $user_id, $user_id, $requester_id);

        if ($stmt->execute()) {
            echo "<p class='success-message'>Friend request accepted!</p>";
        } else {
            echo "<p class='error-message'>Error accepting friend request: " . $stmt->error . "</p>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends | Academic Clan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homepagestyle.css">
</head>
<body>
    <main class="main-content">
        
   
    <section class="section">
            <h1>People You May Know</h1>
            
            <div class="groups-container">
                <?php if ($user_result->num_rows > 0): ?>
                    <!-- Display users who can receive friend requests -->
                    <?php while ($user = $user_result->fetch_assoc()): ?>
                        <div class="group-item">
                            <h2><?php echo htmlspecialchars($user['firstName']) . ' ' . htmlspecialchars($user['lastName']); ?></h2>
                            <p><strong>Bio:</strong> <?php echo htmlspecialchars($user['bio']); ?></p>
                            
                            <form action="Friend.php" method="post">
                                <input type="hidden" name="receiver_user_id" value="<?php echo htmlspecialchars($user['userId']); ?>">
                                <button type="submit" class="btn btn-primary">Send Friend Request</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No users yet.</p>
                <?php endif; ?>
            </div>
            </section>

            <section class="section">
            <h1>Received Friend Requests</h1>

                        <div class="groups-container">
                        <?php if ($request_result->num_rows > 0): ?>
                            <!-- Display received friend requests -->
                            <?php while ($request = $request_result->fetch_assoc()): ?>
                                <div class="group-item">
                                <h2><?php echo htmlspecialchars($request['firstName']) . ' ' . htmlspecialchars($request['lastName']); ?></h2>
                            <p><strong>Bio:</strong> <?php echo htmlspecialchars($request['bio']); ?></p>
                            
                            <!-- Accept Friend Request form -->
                            <form action="Friend.php" method="post">
                                <input type="hidden" name="requester_id" value="<?php echo htmlspecialchars($request['requester_id']); ?>">
                                <button type="submit" class="btn btn-success">Accept</button>
                            </form>
                        </div>


                    <?php endwhile; ?>
                <?php else: ?>
                    <p>You have no received friend requests yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
