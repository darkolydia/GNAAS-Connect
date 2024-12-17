<?php
include 'Lnavbar.php';
include 'sidebar.php';
include 'db_connection.php'; // Database connection
session_start();

// Check if the user is logged in by verifying the session
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit(); // Make sure no further code is executed
}

// Fetch current user's data
$userID = $_SESSION['user_id'];
$user_data = null;

$user_query = "SELECT userID, email, firstName, lastName, profilePicture, bio FROM users WHERE userID = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $userID);
$stmt->execute();
$user_result = $stmt->get_result();
if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
}

// Default query for groups
$groups_query = "SELECT groupName, groupID FROM groupInfo";
$groups_result = $conn->query($groups_query);

// Handle search for Groups
if (isset($_POST['search_groups'])) {
    $searchTerm = $_POST['group_search'];
    if (!empty($searchTerm)) {
        // Search both groupName and groupDescription in the groupInfo table
        $searchTerm = "%" . $searchTerm . "%";
        $groups_query = "SELECT groupName, groupID FROM groupInfo WHERE groupName LIKE ? OR groupDescription LIKE ?";
        $stmt = $conn->prepare($groups_query);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $groups_result = $stmt->get_result();
    }
}

// Fetch posts created by the user
$posts_query = "SELECT postID, title, content, datePosted FROM posts";
$stmt = $conn->prepare($posts_query);
$stmt->execute();
$posts_result = $stmt->get_result();

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['postId'])) {
    $postId = $_POST['postId'];

    // Delete post from the database
    $deleteQuery = "DELETE FROM posts WHERE postID = ? AND userID = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("is", $postId, $userID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Post deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete post.']);
    }
    exit; // Exit after processing the post deletion
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage | GNAAS Connect</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <!-- CSS -->
    <link rel="stylesheet" href="homepagestyle.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Main Content -->
        <main class="main-content">
            <!-- User Profile Section -->
            <section id="profile-section" class="section">
                <h1>User Profile</h1>
                <div class="profile-details">
                    <img src="OIP.jpeg" alt="User Image" class="profile-img">
                    <div class="profile-info">
                        <?php if ($user_data): ?>
                            <h2><?php echo htmlspecialchars($user_data['firstName'] . ' ' . $user_data['lastName']); ?></h2>
                            <p>Email: <?php echo htmlspecialchars($user_data['email']); ?></p>
                            <p>Student ID: <?php echo htmlspecialchars($user_data['userID']); ?></p>
                        <?php else: ?>
                            <p>User data not available.</p>
                        <?php endif; ?>
                        <div class="profile-actions">
                            <a href="profilepage.php" class="btn btn-primary">Edit</a>
                            <a href="login.php" class="btn btn-signout">Sign Out</a>
                            <a href="deleteaccount.php" class="btn btn-danger">Delete Account</a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Groups Section -->
            <section id="groups-section" class="section">
                <h1>Groups</h1>
                <div class="group-actions">
                    <a href="creategroup.php" class="btn btn-primary"><i class="fas fa-plus"></i> Create Group</a>
                    <a href="joingroup.php" class="btn btn-secondary"><i class="fas fa-sign-in-alt"></i> Join Group</a>
                    <form method="post" class="search-form">
                        <input type="text" name="group_search" placeholder="Search groups..." class="search-input">
                        <button type="submit" name="search_groups" class="btn btn-search"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <div class="group-list">
                    <?php if ($groups_result->num_rows > 0): ?>
                        <?php while ($group = $groups_result->fetch_assoc()): ?>
                            <div class="group-item">
                                <h2><?php echo htmlspecialchars($group['groupName']); ?></h2>
                                <a href="viewgroup.php?groupID=<?php echo htmlspecialchars($group['groupID']); ?>" class="btn btn-secondary">View Group</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No groups found.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Posts Section (Replacing Events Section) -->
            <section id="posts-section" class="section">
                <h1> Posts</h1>
                <div class="post-actions">
                    <a href="editpost.php" class="btn btn-primary"><i class="fas fa-plus"></i> Create Post</a>
                </div>
                <div class="groups-container">
                    <?php if ($posts_result->num_rows > 0): ?>
                        <?php while ($post = $posts_result->fetch_assoc()): ?>
                            <div class="group-item">
                                <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                                <p><strong>Posted on:</strong> <?php echo date("F j, Y", strtotime($post['datePosted'])); ?></p>
                                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            </div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No posts available. Create a new post!</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Event listener for delete button
        document.querySelectorAll('.delete-post-btn').forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.getAttribute('data-id');

                // SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete this post?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request to delete the post
                        fetch('', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'postId=' + postId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Show success message and remove the post from the page
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Post Deleted',
                                    text: data.message,
                                    didClose: () => {
                                        location.reload();
                                    }
                                });
                            } else {
                                // Show error message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
