
<nav class="top-nav">
    <div class="nav-left">
        <a class="nav-logo">GNAAS Connect</a>
    </div>
    <div class="nav-right">
        <a href="logout.php" class="dropdown-item">Sign Out</a>
        <a href="notifications.php" class="nav-link">Notifications</a>
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
