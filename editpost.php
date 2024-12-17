<?php
include 'navbar.php';
include 'sidebar.php';
include 'db_connection.php';

session_start();

// Check if the user is logged in by verifying the session
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit(); // Make sure no further code is executed
}

// Fetch current user's data
$userId = $_SESSION['user_id'];
$response = []; // To hold response status and message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get post data from the form
    $title = isset($_POST['title']) ? trim($_POST['title']) : null;
    $content = isset($_POST['content']) ? trim($_POST['content']) : null;
    $postType = isset($_POST['postType']) ? trim($_POST['postType']) : null;
    $visibility = isset($_POST['visibility']) ? trim($_POST['visibility']) : 'public'; // Default to public
    $mediaUrl = isset($_POST['mediaUrl']) ? trim($_POST['mediaUrl']) : null;

    if (!$title || !$content || !$postType) {
        // Handle error if title, content, or post type is missing
        echo "Title, content, and post type are required!";
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert the new post into the 'posts' table
        $insertPostQuery = "INSERT INTO posts (userId, title, content, postType, visibility, datePosted, mediaUrl) 
                            VALUES (?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = $conn->prepare($insertPostQuery);
        $stmt->bind_param("ssssss", $userId, $title, $content, $postType, $visibility, $mediaUrl);
        $stmt->execute();

        if ($stmt->error) {
            throw new Exception("Error inserting post: " . $stmt->error);
        }

        if ($stmt->affected_rows > 0) {
            // Get the ID of the newly created post
            $postId = $stmt->insert_id;
            $notificationContent = "created a post titled, $title";

            // Create notification for the post creator
            $notificationQuery = "INSERT INTO notifications (userId, type, referenceId, content, dateSent) 
                                   VALUES (?, 'post', ?, ?, NOW())";
            $stmt = $conn->prepare($notificationQuery);

            // Insert notification for the post creator
            $stmt->bind_param("sis", $userId, $postId, $notificationContent);
            $stmt->execute();

            if ($stmt->error) {
                throw new Exception("Error inserting notification: " . $stmt->error);
            }

            // Check if the notification was inserted successfully
            if ($stmt->affected_rows > 0) {
                // Commit the transaction
                $conn->commit();
                $response = [
                    'status' => 'success',
                    'message' => 'Post created successfully!',
                    'redirect' => 'myposts.php' // Redirect after successful post creation
                ];
            } else {
                throw new Exception("Notification creation failed.");
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error creating post: ' . $stmt->error
            ];
            throw new Exception("Post creation failed.");
        }
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollback();
        $response = [
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add/Edit Post | Academic Clan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homepagestyle.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .post-form {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .post-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .post-form input, .post-form select, .post-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .post-form textarea {
            resize: vertical;
        }

        .post-form button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .post-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <main class="main-content">
        <section class="section">
            <h1>Add/Edit Post</h1>
            <form action="" method="post" class="post-form">
                
                <label for="title">Title</label>
                <input type="text" id="title" name="title" placeholder="Enter Title" required>

                <label for="content">Post Content</label>
                <textarea id="content" name="content" rows="4" placeholder="Enter your post content" required></textarea>

                <label for="postType">Post Type</label>
                <select id="postType" name="postType" required>
                    <option value="" disabled selected>Select post type</option>
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                    <option value="video">Video</option>
                    <option value="link">Link</option>
                </select>

                <label for="visibility">Visibility</label>
                <select id="visibility" name="visibility">
                    <option value="public">Public</option>
                    <option value="private">Private</option>
                    <option value="friends">Friends</option>
                </select>

                <label for="mediaUrl">Media URL (if applicable)</label>
                <input type="text" id="mediaUrl" name="mediaUrl" placeholder="Enter media URL (optional)">

                <button type="submit" class="btn btn-primary">Save Post</button>
            </form>
        </section>
    </main>
    <script>
        <?php if (!empty($response)) : ?>
            Swal.fire({
                icon: '<?php echo $response['status']; ?>',
                title: '<?php echo ucfirst($response['status']); ?>',
                text: '<?php echo $response['message']; ?>',
                <?php if (isset($response['redirect'])) : ?>
                didClose: () => {
                    window.location.href = '<?php echo $response['redirect']; ?>';
                }
                <?php endif; ?>
            });
        <?php endif; ?>
    </script>
</body>
</html>
