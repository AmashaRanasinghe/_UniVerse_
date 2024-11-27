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
    <?php include('../admin/sidebar.php') ?>
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
                <p>Hello!, Primary Admin!</p>
            </div>
        </div>

        <!-- display content -->
        <div class="cont-p-table-wrapper">
            <div class="search-wrapper">
                <h1>Active Mentorships</h1>
                <form method="GET" action="">
                    <input class="search-input" type="text" name="search" placeholder="Search by ID or Name" required>
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
                        <form method='POST' action='../admin/funcs/mentorship_management.php'>
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
                    <input class="search-input" type="text" name="search" placeholder="Search by ID or Name" required>
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
                        <form method='POST' action='../admin/funcs/mentorship_management.php'>
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
        <script>
            function searchContent() {
                const input = document.querySelector('.search-input').value.toLowerCase();
                const table = document.querySelector('.cont-p-table-wrapper table tbody');
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

</html>