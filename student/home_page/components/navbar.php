<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        <?php include("../../styles/navbar.css") ?>
    </style>
</head>

<body>
    <?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        echo "Not connected";
        exit;
    }

    $id = $_SESSION['user_id'];
    include('../../connection.php');

    $userQuery = "
        SELECT 
            u.firstname, 
            u.lastname, 
            s.profile_picture 
        FROM 
            users u 
        LEFT JOIN 
            student s 
        ON 
            u.user_id = s.user_id 
        WHERE 
            u.user_id = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $userData = $userResult->fetch_assoc();

    $profileImage = $userData['profile_picture'] ?? 'default-profile.png';
    $firstName = htmlspecialchars($userData['firstname'] ?? 'Guest');
    $lastName = htmlspecialchars($userData['lastname'] ?? '');
    ?>

    <nav class="navbar">
        <div class="logo">
            <div class="sidebar-header">
                <div class="logo">
                    <h1>Uni<span class="logo-span">Verse</span></h1>
                </div>
            </div>
        </div>

        <div class="navbar-items" id="navbar-items">
            <li class="navbar-list">
                <a href="../home_page/notes.php" class="active" id="item"><i class="fas fa-book"></i> Notes</a>
                <a href="../home_page/videos.php" id="item"><i class="fas fa-video"></i> Videos</a>
                <a href="../home_page/post.php" id="item"><i class="fas fa-paper-plane"></i> Posts</a>
                <a href="../home_page/mentorships.php" id="item"><i class="fas fa-user-graduate"></i> Mentorships</a>
                <a href="../home_page/groups.php" id="item"><i class="fas fa-users"></i> Groups</a>
            </li>
        </div>

        <div class="profile-wrapper">
            <div class="profile-dropdown">
                <a href="#" class="profile-link">
                    <img src="../../uploads/<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Picture" class="profile-img">
                </a>
                <div class="dropdown-menu">
                    <div class="dropdown-header">
                        <img src="../../uploads/<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Picture" class="dropdown-profile-img">
                        <span><?php echo "$firstName $lastName"; ?></span>
                    </div>
                    <a href="../home_page/update_profile.php"><i class="fas fa-user-edit"></i> Edit User Details</a>
                    <a href="../home_page/notifications.php"><i class="fas fa-bell"></i> Notifications</a>
                    <a href="../home_page/collection.php"><i class="fas fa-box"></i> Collections</a>
                    <a href="../home_page/mygroups.php"><i class="fas fa-users"></i> My Groups</a>
                    <a href="../../index.html"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbarItems = document.querySelectorAll('.navbar-list a');

            const activeItem = localStorage.getItem('activeNavItem');
            if (activeItem) {
                navbarItems.forEach(item => {
                    item.classList.remove('active');
                    if (item.href === activeItem) {
                        item.classList.add('active');
                    }
                });
            }

            navbarItems.forEach(item => {
                item.addEventListener('click', function(event) {
                    event.preventDefault();

                    navbarItems.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');

                    localStorage.setItem('activeNavItem', this.href);
                    window.location.href = this.href;
                });
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const profileLink = document.querySelector('.profile-link');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            // Function to toggle the dropdown menu visibility
            const toggleDropdown = (event) => {
                event.preventDefault();
                dropdownMenu.classList.toggle('show');
            };

            // Close the dropdown if clicked outside
            const closeDropdown = (event) => {
                if (!profileLink.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.remove('show');
                }
            };

            // Event listeners for toggling and closing the dropdown
            profileLink.addEventListener('click', toggleDropdown);
            window.addEventListener('click', closeDropdown);

            // Ensure dropdown menu closes when Esc key is pressed
            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    dropdownMenu.classList.remove('show');
                }
            });
        });
    </script>
</body>

</html>