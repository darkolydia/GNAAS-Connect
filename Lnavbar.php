

<nav class="top-nav">
    <div class="nav-left">
        <a href="homepage.php" class="nav-logo">GNAAS Connect</a>
    </div>
    <div class="nav-right">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- Show Sign Up link if user is not logged in -->
            <a href="signup.php" class="nav-link">Sign Up</a>
        <?php else: ?>
            <!-- Show Profile dropdown if user is logged in -->
            <div class="profile-container">
                <a href="#" class="nav-link profile-icon">
                    Profile
                </a>
                <div class="dropdown-menu">
                    <a href="profile.php" class="dropdown-item">View Profile</a>
                    <a href="logout.php" class="dropdown-item">Sign Out</a>
                </div>
            </div>
        <?php endif; ?>
        <a href="about.php" class="nav-link">About</a>
        <a href="contact.php" class="nav-link">Contact</a>
    </div>
</nav>

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

<!-- Style for dropdown -->
<style>
    .profile-container {
        position: relative;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background-color: #fff;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        min-width: 160px;
        z-index: 1;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        padding: 8px 16px;
        text-decoration: none;
        color: black;
        display: block;
    }

    .dropdown-item:hover {
        background-color: #f1f1f1;
    }

    .profile-icon {
        cursor: pointer;
    }
</style>
