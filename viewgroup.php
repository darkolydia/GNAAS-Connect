<?php
include 'navbar.php';
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
                    // Fetch the member count for each group
                    $member_query = "SELECT COUNT(*) as member_count FROM isamember WHERE groupId = ?";
                    $stmt = $conn->prepare($member_query);
                    $stmt->bind_param("i", $group['groupID']);
                    $stmt->execute();
                    $member_result = $stmt->get_result();
                    $members = $member_result->fetch_assoc();

                    // Fetch upcoming events for each group
                    $event_query = "SELECT eventName, dateScheduled FROM createEvent WHERE hostedBy = ?";
                    $stmt = $conn->prepare($event_query);
                    $stmt->bind_param("i", $group['groupID']);
                    $stmt->execute();
                    $event_result = $stmt->get_result();
                    ?>

                    <div class="group-header">
                        <div class="group-info">
                            <h1>Group Name: <?php echo htmlspecialchars($group['groupName']); ?></h1>
                            <p><strong>Date Established:</strong> <?php echo date("F j, Y", strtotime($group['dateCreated'])); ?></p>
                            <p><strong>Members:</strong> <?php echo $members['member_count']; ?> / <?php echo $group['numLimit']; ?></p>
                        </div>
                    </div>

                    <div class="group-details">
                        <h3>Description</h3>
                        <p><?php echo htmlspecialchars($group['groupDescription']); ?></p>

                        <h3>Schedule</h3>
                        <p><?php echo htmlspecialchars($group['meetingTimes']); ?></p>

                        <h3>Upcoming Events</h3>
                        <?php if ($event_result->num_rows > 0): ?>
                            <ul>
                                <?php while ($event = $event_result->fetch_assoc()): ?>
                                    <li>
                                        <?php echo htmlspecialchars($event['eventName']); ?> - <?php echo date("F j, Y", strtotime($event['dateScheduled'])); ?>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p>No upcoming events.</p>
                        <?php endif; ?><br>
                        
                        <br>-------------------------------------------------------------<br>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No groups available.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
