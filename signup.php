
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Form</title>
</head>
<body>
    <h2>Simple HTML Form</h2>

    <form action="submit.php" method="post">
        <div>
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter your full name" required>
        </div>
        
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>
        
        <div>
            <label for="message">Message:</label>
            <textarea id="message" name="message" placeholder="Enter your message" rows="4" required></textarea>
        </div>
        
        <div>
            <button type="submit">Submit</button>
        </div>
    </form>
</body>
</html>
