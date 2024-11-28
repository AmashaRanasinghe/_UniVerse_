<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../uniVerse/connection.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <style>
        <?php include("../styles/content.css") ?>
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <?php include('../../uniVerse/secondary_admins/sa_sidebar.php') ?>
    <div class="dash">
        <div class="dash-header">
            <div class="dash-h-left">
                <h1>Content Management</h1>
                <?php
                date_default_timezone_set('Asia/Colombo');
                echo date("l, F j, Y g:i A", time())
                ?>
            </div>

            <div class="dash-h-right">
                <!-- some info to be added -->
                <?php
                // $admin_email = $_SESSION['email'];
                // $admin_query = "SELECT type FROM admin WHERE email = ?";
                // $stmt = $conn->prepare($admin_query);
                // $stmt->bind_param("s", $admin_email);
                // $stmt->execute();
                // $admin_result = $stmt->get_result();
                // $admin_row = $admin_result->fetch_assoc();
                // $admin_type = $admin_row['type'];
                // echo "<p>Hello!, " . $admin_type . "</p>";
                ?>
            </div>
        </div>

        <!-- display content -->
        <div class="cont-p-table-wrapper">
            <div class="search-wrapper">
                <h1>Pending Approvals</h1>
                <form method="GET" action="">
                    <input class="serach-input" type="text" name="search" placeholder="Search by ID or Name" required>
                    <button id="search-button" class="btn btn-black" type="submit">Search</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $sql = "SELECT `content_id`, `student_id`, `content_type`, `title`, `description` FROM `content` WHERE `status` = 'Pending Approval'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                    <td>" . $row["content_id"] . "</td>
                    <td>" . $row["content_type"] . "</td>
                    <td>" . $row["title"] . "</td>
                    <td>
                        <form method='POST' action='../secondary_admins/funcs/sa_content_update.php'>
                            <input type='hidden' name='content_id' value='" . $row["content_id"] . "'>
                            <button class='btn btn-green' name='action' value='accept'><i class='fas fa-check'></i></button>
                            <button class='btn btn-black' name='action' value='pending'><i class='fas fa-clock'></i></button>
                            <button class='btn btn-red' name='action' value='reject'><i class='fas fa-times'></i></button>
                        </form>
                    </td>
                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No content found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <!-- reported content -->
        <div class="cont-r-table-wrapper">
            <div class="search-wrapper">
                <h1>Flagged Content</h1>
                <form method="GET" action="">
                    <input class="serach-input" type="text" name="search" placeholder="Search by ID or Name" required>
                    <button id="search-button" class="btn btn-black" type="submit">Search</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    error_reporting(E_ALL);
                    ini_set('display_errors', 1);
                    global $conn;
                    $sql = "SELECT `content_id`, `student_id`, `content_type`, `title`, `description`, `file_url` FROM `content` WHERE `status` = 'Rejected'";
                    $result = $conn->query($sql);

                    // Check for SQL errors
                    if (!$result) {
                        echo "Error: " . $conn->error;
                    } elseif ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                    <td>" . $row["content_id"] . "</td>
                    <td>" . $row["content_type"] . "</td>
                    <td>" . $row["title"] . "</td>
                    <td><img src='" . $row["file_url"] . "' style='width: 100px; height: 100px;'></td>
                    <td>
                        <form method='POST' action='../secondary_admins/funcs/sa_content_update.php'>
                            <input type='hidden' name='content_id' value='" . $row["content_id"] . "'>
                            <button class='btn btn-green' name='action' value='accept'><i class='fas fa-check'></i></button>
                            <button class='btn btn-black' name='action' value='pending'><i class='fas fa-clock'></i></button>
                            <button class='btn btn-red' name='action' value='reject'><i class='fas fa-times'></i></button>
                        </form>
                    </td>
                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No content found</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>