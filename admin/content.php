<?php
session_start();
include('../connection.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content</title>
    <style>
        <?php include("../styles/content.css") ?>
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <?php include('../admin/sidebar.php') ?>
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
                <p>Hello!, Primary Admin!</p>
            </div>
        </div>

        <!-- display content -->
        <div class="cont-r-table-wrapper">
            <div class="search-wrapper">
                <h1>Pending Approvals</h1>
                <form method="GET" action="">
                    <input class="search-input" type="text" name="search" placeholder="Search by ID or Name" required>
                    <button id="search-button" class="btn btn-black" type="submit">Search</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Title</th>
                        <!-- <th>Image</th> -->
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
                        <form method='POST' action='../admin/funcs/content_update.php'>
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
                    <input class="search-input" type="text" name="search" placeholder="Search by ID or Name" required>
                    <button id="search-button" class="btn btn-black" type="submit" onclick="searchContentById()">Search</button>
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
                        <form method='POST' action='../admin/funcs/content_update.php'>
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

    <script>
        function searchContent() {
            const input = document.querySelector('.search-input').value.toLowerCase();
            const table = document.querySelector('.cont-r-table-wrapper table tbody');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const idCell = rows[i].getElementsByTagName('td')[0];
                const firstNameCell = rows[i].getElementsByTagName('td')[1];
                if (idCell || firstNameCell) {
                    const idText = idCell.textContent || idCell.innerText;
                    const firstNameText = firstNameCell.textContent || firstNameCell.innerText;
                    if (input === "" || idText.toLowerCase().indexOf(input) > -1 || firstNameText.toLowerCase().indexOf(input) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }

        document.getElementById('search-button').addEventListener('click', function(event) {
            event.preventDefault();
            searchContent();
        });
    </script>
</body>