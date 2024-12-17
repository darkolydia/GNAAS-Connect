<?php include 'Nnavbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | GNAAS Connect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homepagestyle.css">
    <style>
        /* Pento-Box Design Specific Styles */
        .about-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .about-box {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }

        .about-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .about-box h2 {
            font-size: 22px;
            color: #3a86c9;
            margin-bottom: 15px;
        }

        .about-box p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }

        .about-icon {
            font-size: 40px;
            color: #3a86c9;
            margin-bottom: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .about-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="main-content">
        <section class="section">
            <h1>About Academic Clan</h1>
            <div class="about-container">
                <div class="about-box">
                    <div class="about-icon">🎯</div>
                    <h2>Our Mission</h2>
                    <p>
                        To enhance student collaboration by providing a platform where they can create and join groups, manage posta, and find friends seamlessly.
                    </p>
                </div>
                <div class="about-box">
                    <div class="about-icon">💡</div>
                    <h2>Why Academic Clan?</h2>
                    <p>
                        Current Ashesi Adventist Students often face challenges in connecting with  keeping track of schedules. GNAAS Connect  simplifies this process by fostering a collaborative development environment.
                    </p>
                </div>
                <div class="about-box">
                    <div class="about-icon">⚙️</div>
                    <h2>How It Works</h2>
                    <p>
                        GNAAS Connect allows users to create profiles, search for groups, form groups, create friends, and manage their posts, all in one user-friendly platform.
                    </p>
                </div>
                <div class="about-box">
                    <div class="about-icon">🌍</div>
                    <h2>Join Our Community</h2>
                    <p>
                        Be part of a growing community of Adventist students dedicated to collaborative success. Together, we achieve more!
                    </p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
