<?php
session_start();

include('../connection.php');

// fetchin'
$sql = "SELECT id, fname, lname, email FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups</title>
    <style>
        <?php include("../styles/discussion.css") ?>
    </style>
</head>

<body>
    <?php require('../admin/sidebar.php'); ?>
    <div class="dash">
        <div class="dash-header">
            <div class="dash-h-left">
                <h1>Discussion Management</h1>
                <?php
                date_default_timezone_set('Asia/Colombo');
                echo date("l, F j, Y g:i A", time())
                ?>
            </div>

            <div class="dash-h-right">
                <!-- some info will be here -->
                <?php
                $admin_email = $_SESSION['email'];
                $admin_query = "SELECT type FROM admin WHERE email = ?";
                $stmt = $conn->prepare($admin_query);
                $stmt->bind_param("s", $admin_email);
                $stmt->execute();
                $admin_result = $stmt->get_result();
                $admin_row = $admin_result->fetch_assoc();
                $admin_type = $admin_row['type'];
                echo "<p>Hello!, " . $admin_type . "</p>";
                ?>
            </div>
        </div>

        <!-- active groups -->
        <div class="grp-a-table-wrapper">
            <div class="search-wrapper">
                <h1>Active Discussions</h1>
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
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                    <td>" . $row["id"] . "</td>
                    <td>" . $row["fname"] . "</td>
                    <td>" . $row["lname"] . "</td>
                    <td>" . $row["email"] . "</td>
                    <td>
                        <button class='btn btn-green'>View</button>
                        <button class='btn btn-red'>Reject</button>
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


        <!-- flagged discussions -->
        <div class="grp-f-table-wrapper">
            <div class="search-wrapper">
                <h1>Flagged Discussions</h1>
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
                    session_start();

                    include('../connection.php');

                    // fetchin'
                    $sql = "SELECT id, fname, lname, email FROM users";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                    <td>" . $row["id"] . "</td>
                    <td>" . $row["fname"] . "</td>
                    <td>" . $row["lname"] . "</td>
                    <td>" . $row["email"] . "</td>
                    <td>
                        <button class='btn btn-green'>Accept</button>
                        <button class='btn btn-black'>Pending</button>
                        <button class='btn btn-red'>Delete</button>
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