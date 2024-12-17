<?php
include 'Navbar.php';
include 'sidebar.php';
// Include necessary files
include 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo 'User not logged in.';
    exit(); // Exit if no session is found
}

// Retrieve the logged-in user's full name from the session
$userFullName = $_SESSION['user_name'];  // Assuming the full name is stored in session
$loggedInUserId = $_SESSION['user_id']; // Assuming the user_id is stored in session

// Fetch notifications for the logged-in user
$query = "SELECT * FROM notifications ORDER BY dateSent DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$notificationsResult = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | GNAAS Connect</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="homepagestyle.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Custom styles for notifications */
        .notifications-container {
            padding: 20px;
        }

        .notification-item {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .notification-item .date {
            font-size: 0.9em;
            color: gray;
        }

        .notification-item strong {
            display: block;
            font-size: 1.1em;
            margin-bottom: 5px;
        }

        .notification-item.read {
            background-color: #e9ecef;
        }

        .notification-item.unread {
            background-color: #f1f3f5;
        }

        .notification-item.cleared {
            background-color: #d3d3d3;
            text-decoration: line-through;
        }

        /* Add a margin-top for better spacing from the navbar */
        .main-content {
            margin-top: 20px;
        }

        /* Custom Flexbox styles to align button and heading */
        .notifications-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notifications-header h2 {
            margin: 0;
        }
    </style>
</head>
<body>

    <!-- Main Content Area -->
    <div class="container-fluid">
        <div class="row">
            <!-- Content Area -->
            <div class="col-md-9 main-content">
                <div class="notifications-container">
                    <div class="notifications-header">
                        <!-- "Your Notifications" text and Clear All button in the same line -->
                        <h2>Your Notifications</h2>
                        <button id="clearAllBtn" class="btn btn-warning">Clear All Notifications</button>
                    </div>

                    <?php if ($notificationsResult->num_rows > 0): ?>
                        <ul class="list-group" id="notificationsList">
                            <?php while ($notification = $notificationsResult->fetch_assoc()): ?>
                                <?php
                                    // Check if the logged-in user created the post
                                    $notificationContent = $notification['content'];

                                    // If the logged-in user is the one who created the post, prepend "You"
                                    if (strpos($notificationContent, "created a post titled") !== false) {
                                        // If the logged-in user created the post
                                        if ($notification['userId'] == $loggedInUserId) {
                                            $notificationContent = "You " . substr($notificationContent, 0); // "You created a post titled ..."
                                        } else {
                                            // Otherwise, prepend the full name of the user who created the post
                                            $notificationContent = $userFullName . " " . substr($notificationContent, 0); // "FullName created a post titled ..."
                                        }
                                    }

                                    // Default to "unread" class for all notifications
                                    $notificationClass = 'unread';
                                ?>
                                <li class="list-group-item notification-item <?php echo $notificationClass; ?>">
                                    <strong><?php echo htmlspecialchars($notificationContent); ?></strong>
                                    <span class="date"><?php echo date("F j, Y, g:i a", strtotime($notification['dateSent'])); ?></span>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p class="alert alert-info">No notifications yet!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    
    <!-- Custom JavaScript to hide notifications when the user clicks "Clear All" -->
    <script>
        // Event listener for the "Clear All" button
        document.getElementById('clearAllBtn').addEventListener('click', function() {
            // Get all notifications and mark them as cleared
            var notifications = document.querySelectorAll('.notification-item');
            notifications.forEach(function(notification) {
                notification.classList.add('cleared');
                notification.style.display = 'none';  // Hides the notification from view
            });
        });
    </script>

</body>
</html>

