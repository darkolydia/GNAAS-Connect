<!-- navbar.php -->
<nav class="top-nav">
    <div class="nav-left">
        <a class="nav-logo">GNAAS Connect</a>
    </div>
    <div class="nav-right">
        <a href="about.php" class="nav-link">About</a>
        <a href="contact.php" class="nav-link">Contact</a>
        <a href="notifications.php" class="nav-link">Notifications</a>

 
        <div class="profile-icon">
            <img src="OIP.jpeg" alt="Profile Picture" class="avatar">
            <div class="dropdown-menu">
                <a href="profilepage.php">Profile</a>
                <a href="index.php">Sign Out</a>
            </div>
        </div>
    </div>
    <!-- JavaScript for Dropdown Menu -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileIcon = document.querySelector('.profile-icon');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            profileIcon.addEventListener('click', function(event) {
                dropdownMenu.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            window.addEventListener('click', function(event) {
                if (!profileIcon.contains(event.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        });
    </script>
</nav>
