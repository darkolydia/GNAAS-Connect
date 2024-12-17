<?php
include 'Lnavbar.php';
include 'sidebar.php';
include 'db_connection.php'; // Database connection

session_start(); // Start the session

$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupName = $_POST['groupName'];
    $maxMembers = $_POST['numLimit'];
    $description = $_POST['groupDescription'];

    // Check if user is logged in and has a valid session ID
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        $error_message = "User not logged in. Please log in first.";
    } else {
        // Get the current user's ID from session
        $userID = $_SESSION['user_id'];

        // Server-side validation
        if ($maxMembers < 2) {
            $error_message = "Max members must be at least 2.";
        } elseif (strlen($description) > 50) {
            $error_message = "Group description must not exceed 50 characters.";
        } else {
            // Insert group into the database
            $query = "INSERT INTO groupinfo (groupName, numLimit, groupDescription, dateCreated) 
                      VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sis", $groupName, $maxMembers, $description);

            if ($stmt->execute()) {
                // Get the last inserted groupID
                $groupID = $conn->insert_id;
                
                // // Define the user role (e.g., 'Admin') and user type (e.g., 'student')
                // $groupRole = 'Admin';
                // $memberType = 'student'; // Assuming the user is a student. Change if needed

                // Insert the user as a member of the newly created group
                $insert_member_query = "INSERT INTO isamember (groupId, memberId, groupRole) 
                                         VALUES (?, ?, ?)";
                $stmt_member = $conn->prepare($insert_member_query);
                $stmt_member->bind_param("iis", $groupID, $userID, $groupRole);

                if ($stmt_member->execute()) {
                    $success_message = "Group created successfully, and you're added as a member!";
                    header("Location: mygroups.php");
                    exit();
                } else {
                    $error_message = "Failed to add you as a member: " . $stmt_member->error;
                    header("Location: creategroup.php");
                    exit();
                }
            } else {
                $error_message = "An error occurred: " . $stmt->error;
                header("Location: creategroup.php");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group | Academic Clan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homepagestyle.css">
</head>
<body>
    <main class="main-content">
        <section class="section">
            <h1>Create a New Group</h1>
            <?php if (!empty($error_message)): ?>
                <p class="error-message" style="color:red;"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <p class="success-message" style="color:green;"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <form action="creategroup.php" method="post" class="group-form">

                <label for="group-name">Group Name</label>
                <input type="text" id="group-name" name="groupName" placeholder="Enter group name" required>

                <label for="max-members">Max Members</label>
                <input type="number" id="max-members" name="numLimit" min="2" placeholder="Enter max number of members" required>

                <label for="description">Group Description</label>
                <textarea id="description" name="groupDescription" rows="4" placeholder="Briefly describe the group" required></textarea>

                <button type="submit" class="btn btn-primary">Create Group</button>
                <a href="homepage.php" class="btn btn-danger">Cancel</a>
            </form>
        </section>
    </main>
</body>
</html>
