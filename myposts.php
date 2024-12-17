<?php
// Include necessary files
include 'navbar.php';
include 'sidebar.php';
include 'db_connection.php'; // Database connection

// Start session to access user data (assuming session is used for login)
session_start();

// Check if the user is logged in by verifying the session
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit(); // Make sure no further code is executed
}

// Get the current user's ID from session (assuming user ID is stored in session)
$userId = $_SESSION['user_id']; 

// Handle post deletion if a POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['postId'])) {
    $postId = $_POST['postId'];

    // Query to delete the post
    $deleteQuery = "DELETE FROM posts WHERE postId = ? AND userId = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("is", $postId, $userId); // Bind the postId and userId parameters
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Successfully deleted the post
        echo json_encode(['status' => 'success', 'message' => 'Post has been successfully deleted.']);
        header("Location: myposts.php"); // Redirect after deleting the post
    } else {
        // Error deleting the post
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete the post.']);
    }
    exit; // Exit after processing the deletion
}

$query = "SELECT postId, title, content, postType, visibility, datePosted FROM posts WHERE userId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userId); // Fetch only the posts of the logged-in user
$stmt->execute();
$result = $stmt->get_result(); // Get the result of the query


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts | GNAAS Connect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homepagestyle.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .post-item {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .post-item button {
            background-color: #e74c3c;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .post-item button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <main class="main-content">
        <div class="section">
            <div class="header-container">
                <h1>My Posts</h1>
                <a href="editpost.php" class="btn btn-primary">Add Post</a>
            </div>
            
            <!-- Post List -->
            <div class="groups-container">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($post = $result->fetch_assoc()): ?>
                        <div class="group-item">
                            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                            <p><strong>Posted on:</strong> <?php echo date("F j, Y", strtotime($post['datePosted'])); ?></p>
                            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            <button class="btn btn-danger delete-post-btn" data-id="<?php echo $post['postId']; ?>">Delete Post</button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No posts available. Create a new post!</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

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
