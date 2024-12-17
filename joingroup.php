<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


include 'Lnavbar.php';
include 'sidebar.php';
include 'db_connection.php'; // Database connection

session_start(); // Start the session

// Initialize variables
$error_message = '';
$success_message = '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch all available groups excluding those the user is already a part of
$group_query = "
    SELECT * 
    FROM groupinfo 
    WHERE groupID NOT IN (
        SELECT groupID 
        FROM isamember 
        WHERE memberId = ?
    )
";
$stmt = $conn->prepare($group_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$group_result = $stmt->get_result();

// Handle Join Group functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $group_id = $_POST['group_id']; // Get the group ID from the form submission

    // Fetch the selected group information
    $group_details_query = "SELECT * FROM groupinfo WHERE groupID = ?";
    $stmt = $conn->prepare($group_details_query);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $group_result = $stmt->get_result();

    if ($group_result->num_rows > 0) {
        $group = $group_result->fetch_assoc();
        
        // Fetch group member count
        $member_query = "SELECT COUNT(*) as member_count FROM isamember WHERE groupID = ?";
        $stmt = $conn->prepare($member_query);
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $member_result = $stmt->get_result();
        $members = $member_result->fetch_assoc();

        if ($members['member_count'] < $group['numLimit']) {
            // Check if the user is already a member
            $check_query = "SELECT * FROM isamember WHERE groupID = ? AND memberId = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("ii", $group_id, $user_id);
            $stmt->execute();
            $check_result = $stmt->get_result();

            if ($check_result->num_rows === 0) {
                // Add user to the group
                $insert_query = "INSERT INTO isamember (groupID, memberId, groupRole) VALUES (?, ?, 'Member')";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("is", $group_id, $user_id);

                if ($stmt->execute()) {
                    $success_message = "You have successfully joined the group!";
                } else {
                    $error_message = "Failed to join the group. Please try again.";
                }
            } else {
                $error_message = "You are already a member of this group.";
            }
        } else {
            $error_message = "This group has reached its member limit.";
        }
    } else {
        $error_message = "Group not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Group | GNAAS Connect </title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homepagestyle.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert library -->
</head>
<body>
    <main class="main-content">
        <section class="section">
            <h1>Available Groups</h1>

            <?php if ($group_result->num_rows > 0): ?>
                <form action="joingroup.php" method="post">
                    <div class="group-list">
                        <?php while ($group = $group_result->fetch_assoc()): ?>
                            <div class="group-item">
                                <h2><?php echo htmlspecialchars($group['groupName']); ?></h2>
                                <p><strong>Members:</strong> 
                                    <?php
                                    $group_id = $group['groupID'];
                                    $member_query = "SELECT COUNT(*) as member_count FROM isamember WHERE groupID = ?";
                                    $stmt = $conn->prepare($member_query);
                                    $stmt->bind_param("i", $group_id);
                                    $stmt->execute();
                                    $member_result = $stmt->get_result();
                                    $members = $member_result->fetch_assoc();
                                    echo $members['member_count'] . ' / ' . $group['numLimit'];
                                    ?>
                                </p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($group['groupDescription']); ?></p>
                                <input type="radio" id="group_<?php echo $group['groupID']; ?>" name="group_id" value="<?php echo $group['groupID']; ?>" required>
                                <label for="group_<?php echo $group['groupID']; ?>">Join this group</label>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Join Group</button>
                </form>
            <?php else: ?>
                <p>No groups available.</p>
            <?php endif; ?>
        </section>
    </main>

    <!-- SweetAlert Trigger -->
    <?php if (!empty($success_message)): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo $success_message; ?>',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "joingroup.php";
            });
        </script>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $error_message; ?>',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>
</body>
</html>
