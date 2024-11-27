<?php
include('../connection.php')
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentorships</title>
    <style>
        <?php include('../../uniVerse/styles/content.css') ?>
    </style>
</head>

<body>
    <?php include('../../uniVerse/secondary_admins/sa_sidebar.php') ?>
    <div class="dash">
        <div class="dash-header">
            <div class="dash-h-left">
                <h1>Mentorship Management</h1>
                <?php
                date_default_timezone_set('Asia/Colombo');
                echo date("l, F j, Y g:i A", time())
                ?>
            </div>

            <div class="dash-h-right">
                <p>Hello!, Secondary Admin!</p>
            </div>
        </div>

        <!-- display content -->
        <div class="cont-p-table-wrapper">
            <div class="search-wrapper">
                <h1>Active Mentorships</h1>
                <form method="GET" action="">
                    <input class="serach-input" type="text" name="search" placeholder="Search by ID or Name" required>
                    <button id="search-button" class="btn btn-black" type="submit">Search</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Mentor ID</th>
                        <th>Student ID</th>
                        <th>Subject Areas</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $sql = "SELECT * FROM mentors WHERE status = 'Active'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                    <td>" . $row["mentor_id"] . "</td>
                    <td>" . $row["student_id"] . "</td>
                    <td>" . $row["subject_area"] . "</td>
                    <td>" . "Rs. ", $row["price"] . "</td>
                    <td>
                        <form method='POST' action='../secondary_admins/funcs/sa_mentorship_management.php'>
                            <input type='hidden' name='ment_id' value='" . $row["mentor_id"] . "'>
                            <button class='btn btn-green' name='action' value='accept'><i class='fas fa-check'></i></button>
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
        <div class="cont-p-table-wrapper">
            <div class="search-wrapper">
                <h1>Inactive Mentorships</h1>
                <form method="GET" action="">
                    <input class="serach-input" type="text" name="search" placeholder="Search by ID or Name" required>
                    <button id="search-button" class="btn btn-black" type="submit">Search</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Mentor ID</th>
                        <th>Student ID</th>
                        <th>Subject Areas</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $sql = "SELECT * FROM mentors WHERE status = 'Inactive'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                    <td>" . $row["mentor_id"] . "</td>
                    <td>" . $row["student_id"] . "</td>
                    <td>" . $row["subject_area"] . "</td>
                    <td>"  . "Rs. ", $row["price"] . "</td>
                    <td>
                        <form method='POST' action='../secondary_admins/funcs/sa_mentorship_management.php'>
                            <input type='hidden' name='ment_id' value='" . $row["mentor_id"] . "'>
                            <button class='btn btn-green' name='action' value='accept'><i class='fas fa-check'></i></button>
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

</body>

</html>