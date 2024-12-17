<?php
include 'navbar.php';
include 'sidebar.php';
include 'db_connection.php'; // Database connection
session_start();

// Get the current user ID from the session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch groups the user is a part of
$user_groups_query = "
    SELECT gi.groupID, gi.groupName, gi.groupDescription, gi.numLimit,
           (SELECT COUNT(*) FROM isAmember WHERE groupId = gi.groupID) as member_count
    FROM groupinfo gi
    JOIN isAmember ia ON gi.groupID = ia.groupId
    WHERE ia.memberId = ?
";
$stmt = $conn->prepare($user_groups_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_groups_result = $stmt->get_result();

// Handle Leave Group functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = isset($_POST['group_id']) ? $_POST['group_id'] : null;

    if ($group_id) {
        // Leave group logic
        $leave_query = "DELETE FROM isAmember WHERE groupId = ? AND memberId = ?";
        $stmt = $conn->prepare($leave_query);
        $stmt->bind_param("ii", $group_id, $user_id);

        if ($stmt->execute()) {
            header("Location: mygroups.php"); // Refresh the page after leaving
            exit();
        } else {
            echo "<p class='error-message'>Error leaving the group: " . $stmt->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Groups | GNAAS Connect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homepagestyle.css">

    <style>

      /* Header container to align header and button horizontally */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            margin: 0; /* Remove default margin from the h1 */
        }

        a.btn {
            margin-left: auto; /* Push button to the right */
        }

    </style>

</head>
<body>
    <main class="main-content">
        <section class="section">
        <div class="header-container">
        <h1>My Groups</h1>
        <a href="creategroup.php" class="btn btn-primary"><i class="fas fa-plus"></i> Create Group</a>
        </div>
            
            <div class="groups-container">
                <?php if ($user_groups_result->num_rows > 0): ?>
                    <!-- Display groups the user is a part of -->
                    <?php while ($group = $user_groups_result->fetch_assoc()): ?>
                        <div class="group-item">
                            <h2><?php echo htmlspecialchars($group['groupName']); ?></h2>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($group['groupDescription']); ?></p>
                            <p><strong>Members:</strong> <?php echo $group['member_count'] . ' / ' . $group['numLimit']; ?></p>
                            <form action="mygroups.php" method="post">
                                <input type="hidden" name="group_id" value="<?php echo $group['groupID']; ?>">
                                <button type="submit" name="action" value="leave" class="btn btn-danger">Leave Group</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>You are not a member of any groups yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
