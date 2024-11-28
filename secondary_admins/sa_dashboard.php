<?php
include('../connection.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        <?php include('../styles/dashboard.css') ?>
    </style>
</head>

<body>

    <?php include('../../uniVerse/secondary_admins/sa_sidebar.php') ?>
    <div class="dash">

        <div class="dash-header">
            <div class="dash-h-left" style="display:inline">
                <h1>Dashboard</h1>
                <?php

                date_default_timezone_set('Asia/Colombo');
                echo date("l, F j, Y g:i A", time())
                ?>
            </div>

            <div class="dash-h-right">
                <p>Hello!, Secondary Admin!</p>
            </div>
        </div>

        <div class="dash-main">
            <div class="overview">
                <div class="overview-item">
                    <h2>Total Users</h2>
                    <?php
                    $query = "SELECT COUNT(*) as total_students FROM users WHERE role = 'Student'";
                    $result = $conn->query($query);
                    if ($result) {
                        $row = $result->fetch_assoc();
                        echo "<p>" . $row['total_students'] . "</p>";
                    } else {
                        echo "<p>0</p>";
                    }
                    ?>
                </div>

                <div class="overview-item">
                    <h2>Total Posts</h2>
                    <?php
                    $query = "SELECT COUNT(*) as total_content FROM content";
                    $result = $conn->query($query);
                    if ($result) {
                        $row = $result->fetch_assoc();
                        echo "<p>" . $row['total_content'] . "</p>";
                    } else {
                        echo "<p>0</p>";
                    }
                    ?>
                </div>

                <div class="overview-item">
                    <h2>Ongoing Activities</h2>
                    <?php
                    $query = "SELECT COUNT(*) as total_pending FROM content WHERE status = 'Pending Approval'";
                    $result = $conn->query($query);
                    if ($result) {
                        $row = $result->fetch_assoc();
                        echo "<p>" . $row['total_pending'] . "</p>";
                    } else {
                        echo "<p>0</p>";
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</body>

</html>