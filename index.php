<?php include 'Lnavbar.php';
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | GNAAS Connect</title>
    <link rel="stylesheet" href="homepagestyle.css">

    <style>
body {
  margin: 0;
  height: 100vh; /* Ensures the body takes up the full height of the viewport */
  position: relative;
  background-image: url('https://transplantfirst.org/wp-content/uploads/2015/01/group.connection.jpg');
  background-size: cover;
  background-position: center;
  background-color: transparent; /* Ensures the background is transparent */
}

body::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.67); /* Semi-transparent black overlay (adjust as needed) */
  z-index: 1; /* Overlay above the image, below the content */
  pointer-events: none; /* Prevents the overlay from blocking interactions */

}

main {
  position: relative;
  z-index: 2; /* Ensures the main content is above the overlay */
}


h1 {
  position: relative;
  color: white;
  text-align: center;
  font-size: 3em;
  margin-top: 20%;
  z-index: 2; /* Ensures the text appears above the overlay */
}

    </style>
</head>
<body>
    <main class="container">
        <!-- Hero Section -->
        <section class="hero section">
            <h1>Welcome to GNAAS Connect!</h1>
            <div class="cta-container">
                <a href="signup.php" class="btn btn-primary">Join Us Now</a>
                <a href="login.php" class="btn btn-secondary">Login</a>
            </div>
        </section>


       
    </main>
    <footer>
        <p>&copy; 2024 GNAAS Connect. All Rights Reserved.</p>
    </footer>
</body>
</html>

