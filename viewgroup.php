<?php
include 'Lnavbar.php';
include 'sidebar.php';
include 'db_connection.php'; // Database connection

// Initialize variables
$error_message = '';

// Fetch all available groups
$group_query = "SELECT * FROM groupinfo";
$stmt = $conn->prepare($group_query);
$stmt->execute();
$group_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Dashboard | Academic Clan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homepagestyle.css">
</head>
<body>
    <main class="main-content">
        <section class="section">
            <?php if ($group_result->num_rows > 0): ?>
                <?php while ($group = $group_result->fetch_assoc()): ?>
                    <?php
                    // Fetch the members of the group
                    $member_query = "SELECT u.firstName, u.lastName FROM isamember im JOIN users u ON im.userID = u.userID WHERE im.groupId = ?";
                    $stmt = $conn->prepare($member_query);
                    $stmt->bind_param("i", $group['groupID']);
                    $stmt->execute();
                    $member_result = $stmt->get_result();
                    ?>

                    <div class="group-header">
                        <div class="group-info">
                            <h1>Group Name: <?php echo htmlspecialchars($group['groupName']); ?></h1>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($group['groupDescription']); ?></p>
                        </div>
                    </div>

                    <div class="group-members">
                        <h3>Members</h3>
                        <ul>
                            <?php if ($member_result->num_rows > 0): ?>
                                <?php while ($member = $member_result->fetch_assoc()): ?>
                                    <li><?php echo htmlspecialchars($member['firstName'] . ' ' . $member['lastName']); ?></li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p>No members found for this group.</p>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <br>-------------------------------------------------------------<br>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No groups available.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
