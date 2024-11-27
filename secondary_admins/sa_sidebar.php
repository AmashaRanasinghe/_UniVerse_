<?php
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();

    echo '<script>alert("Successfully Logged Out!");</script>';
    header("Location: ../admin/login.php");
    session_destroy();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniVerse Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style type="text/css">
        <?php include '../styles/sidebar.css'; ?>
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <h1>Uni<span class="logo-span">Verse</span></h1>
            </div>
        </div>
        <nav class="menu">
            <ul>
                <li class="menu-item"><a href="../secondary_admins/sa_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <!-- <li class="menu-item"><a href="../admin/users.php"><i class="fas fa-users"></i> Users</a></li> -->
                <li class="menu-item"><a href="../secondary_admins/sa_content.php"><i class="fas fa-file-alt"></i> Content</a></li>
                <li class="menu-item"><a href="../secondary_admins/sa_mentorships.php"><i class="fas fa-user-friends"></i></i>Mentorships</a></li>
            </ul>
        </nav>
        <!-- <hr> -->
        <div class="sidebar-footer">
            <ul>
                <li class="menu-item"><a href="#"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li class="menu-item"><a href="?action=logout"><i class="fas fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </div>
    </aside>

    <script>
        // todo : a script to load a default page should be added
    </script>
</body>

</html>