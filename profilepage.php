<?php
// Include necessary files
include 'navbar.php';
include 'sidebar.php';
include 'db_connection.php'; // Database connection



// Check if user is logged in (user_id should exist in the session)
if (empty($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit;
}

// Get the current user's ID from session
$userId = $_SESSION['user_id'];

// Initialize success message
$successMessage = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $bio = $_POST['bio']; // Bio is editable
    $profileImage = $_FILES['profile_image']['name']; // Profile image file name

    // Handle profile image upload if a new image is uploaded
    if (!empty($profileImage)) {
        // Specify the target directory for the image upload
        $targetDir = 'uploads/';
        $targetFile = $targetDir . basename($profileImage);

        // Check if the directory exists, create it if it doesn't
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // Create directory with write permissions
        }

        // Move the uploaded image to the target directory
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
            echo "Image uploaded successfully.";
        } else {
            echo "Error uploading image.";
        }
    }

    // SQL query to update the user's profile information
    $query = "UPDATE users SET bio = ?";
    
    // If a new profile image is uploaded, add it to the query
    if (!empty($profileImage)) {
        $query .= ", profilePicture = ?";
    }
    
    $query .= " WHERE userId = ?";

    // Prepare and execute the SQL query
    if ($stmt = $conn->prepare($query)) {
        if (!empty($profileImage)) {
            // Bind parameters when the image is uploaded
            $stmt->bind_param("sss", $bio, $profileImage, $userId);
        } else {
            // Bind parameters without the image
            $stmt->bind_param("ss", $bio, $userId);
        }
        $stmt->execute(); // Execute the query
        $stmt->close(); // Close the statement

        // Set success message
        $successMessage = "Your profile has been updated successfully!";
    } else {
        echo "Error preparing the update query.";
    }
}

// Query to get the user's profile information from the users table
$query = "SELECT firstName, lastName, gender, email, profilePicture, bio, dateJoined 
          FROM users WHERE userId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId); // Bind the user ID parameter as integer
$stmt->execute();
$result = $stmt->get_result(); // Get the result of the query

// Check if the user data exists
if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile | Academic Clan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homepagestyle.css">
</head>
<body>
    <main class="main-content">
        <section class="section">
            <h1>Edit User Profile</h1>

            <!-- Success Message -->
            <?php if ($successMessage): ?>
                <div class="success-message">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>

            <form class="profile-form" action="profilepage.php" method="post" enctype="multipart/form-data">
                <!-- Profile Image -->
                <div class="form-group">
                    <label for="profile-image">Profile Image</label>
                    <div class="profile-image-container">
                        <!-- Display current profile image if exists -->
                        <?php if (!empty($userData['profilePicture'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($userData['profilePicture']); ?>" alt="Profile Image" class="profile-img">
                        <?php else: ?>
                            <img src="uploads/default-profile.png" alt="Profile Image" class="profile-img">
                        <?php endif; ?>
                        <input type="file" id="profile-image" name="profile_image" accept="image/*">
                    </div>
                </div>

                <!-- Read-Only Fields -->
                <div class="form-group">
                    <label for="student-id">Student ID</label>
                    <input type="text" id="student-id" name="student_id" value="<?php echo htmlspecialchars($userData['userId']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="first_name" value="<?php echo htmlspecialchars($userData['firstName']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="last_name" value="<?php echo htmlspecialchars($userData['lastName']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <input type="text" id="gender" name="gender" value="<?php echo htmlspecialchars($userData['gender']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" readonly>
                </div>

                <!-- Editable Fields -->
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" rows="3" placeholder="Enter your bio"><?php echo htmlspecialchars($userData['bio']); ?></textarea>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
