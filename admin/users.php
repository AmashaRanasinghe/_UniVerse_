<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../connection.php');
session_start();

// search function


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <style>
        <?php include("../styles/users.css") ?>#edit-admin-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            backdrop-filter: blur(5px);
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            /* Ensure it appears below the modal */
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php require('../admin/sidebar.php') ?>
    <div class="dash">
        <div class="dash-header">
            <div class="dash-h-left">
                <h1>User Management</h1>
                <?php
                date_default_timezone_set('Asia/Colombo');
                echo date("l, F j, Y g:i A", time())
                ?>
            </div>

            <div class="dash-h-right">
                <p>Hello!, Primary Admin!</p>
            </div>
        </div>

        <div class="active-users">
            <div class="st-table-wrapper">
                <div class="search-wrapper">
                    <h1>Students</h1>
                    <form method="GET" action="../admin/users.php">
                        <input class="search-input" type="text" name="search" placeholder="Search by ID or Name" required>
                        <button id="search-button" class="btn-add" type="submit" name="search">Search</button>
                    </form>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th class="st-id">ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>University</th>
                            <th>Degree Program</th>
                            <th>Profile Picture</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT s.`student_id`, u.`user_id`, u.`email`, u.`firstname`, u.`lastname`, u.`role`, u.`created_at`, u.`updated_at`, s.`university`, s.`year_of_study`, s.`degree_program`, s.`bio`, s.`profile_picture` 
                                FROM `student` s 
                                INNER JOIN `users` u ON s.`user_id` = u.`user_id`";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                    <td class='st-id'>" . $row["student_id"] . "</td>
                    <td>" . $row["firstname"] . "</td>
                    <td>" . $row["lastname"] . "</td>
                    <td>" . $row["university"] . "</td>
                    <td>" . $row["degree_program"] . "</td>
                    <td><img src='" . $row["profile_picture"] . "' alt='Profile Picture' style='width: 50px; height: 50px; border-radius: 50%;'></td>
                    <td>
                        <button class='btn btn-green'>
                            <i class='fas fa-user'></i>
                        </button>
                        <button class='btn btn-black'>
                            <i class='fas fa-redo'></i>
                        </button>
                        <button class='btn btn-red' onclick='deleteUser(" . $row["user_id"] . ")'>
                            <i class='fas fa-trash'></i>
                        </button>
                    </td>
                  </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10'>No users found</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- admins -->
        <div class="st-table-wrapper">
            <div class="search-wrapper">
                <h1>Administrators</h1>
                <button class="btn-add" type="button" onclick="toggleAddAdminForm()">Add Admin</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="st-id">ID</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require('../connection.php');

                    //fetchin' - should change the user type to admins
                    $asql = "SELECT `user_id`, `email`, `password_hash`, `role`, `created_at`, `updated_at`, `firstname`, `lastname` FROM `users` WHERE `role` IN ('Primary Administrator', 'Secondary Administrator')";
                    $result = $conn->query($asql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                    <td class='st-id'>" . $row["user_id"] . "</td>
                    <td>" . $row["email"] . "</td>
                    <td>" . $row["role"] . "</td>
                    <td>" . $row["created_at"] . "</td>
                    <td>" . $row["updated_at"] . "</td>
                    <td>
                        <button class='btn btn-black' onclick='openEditAdminForm(" . $row['user_id'] . ", \"" . $row['email'] . "\", \"" . $row['firstname'] . "\", \"" . $row['lastname'] . "\")'>
                            <i class='fas fa-edit'></i>
                        </button>

                        <button class='btn btn-red' onclick='deleteUser(" . $row["user_id"] . ")'>
                            <i class='fas fa-trash'></i>
                        </button>
                    </td>
                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No users found</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <form method="POST" action="../admin/funcs/add_admin.php" id="add-admin-form" style="display:none; background-color:white;">
        <h2 style="text-align:center;">Add Administrators</h2>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="firstname" placeholder="First Name" required>
        <input type="text" name="lastname" placeholder="Last Name" required>
        <input type="text" name="Secondary Admininstrator" placeholder="Admin Type">
        <button id="add-ad" class="btn btn-black" name="login">Add</button>
    </form>

    <div class="overlay" id="overlay" onclick="toggleAddAdminForm()"></div>
    <div class="overlays" id="overlays" onclick="togglEditAdminForm()"></div>

    <form method="POST" action="../admin/funcs/edit_admin.php" id="edit-admin-modal" style="display:none;">
        <h2 style="text-align:center;">Edit Administrator</h2>
        <input type="hidden" name="user_id" id="edit-user-id">
        <input type="email" name="email" id="edit-email" placeholder="Email" required>
        <input type="text" name="firstname" id="edit-firstname" placeholder="First Name" required>
        <input type="text" name="lastname" id="edit-lastname" placeholder="Last Name" required>
        <button class="btn btn-blacks" type="submit">Save Changes</button>
        <button class="btn-cancel" type="button" onclick="toggleEditAdminForm()">Cancel</button>
    </form>
    <!-- js script to search users -->
    <script>
        function toggleAddAdminForm() {
            const form = document.getElementById('add-admin-form');
            const overlay = document.getElementById('overlay');
            const isVisible = form.style.display === 'flex';

            form.style.display = isVisible ? 'none' : 'flex';
            overlay.style.display = isVisible ? 'none' : 'block';
        }

        function toggleEditAdminForm() {
            const modal = document.getElementById('edit-admin-modal');
            const overlay = document.getElementById('overlays');
            const isVisible = modal.style.display === 'flex';

            modal.style.display = isVisible ? 'none' : 'flex'; // Set to 'flex' for proper alignment
            overlay.style.display = isVisible ? 'none' : 'block'; // Show overlay when modal is visible
        }

        function deleteUser(userID) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = 'funcs/delete_user.php?id=' + userID;
            }
        }

        function searchUsers() {
            const input = document.querySelector('.search-input').value.toLowerCase();
            const table = document.querySelector('.active-users table tbody');
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
            event.preventDefault(); // Prevent form submission
            searchUsers();
        });

        function openEditAdminForm(userId, email, firstname, lastname) {
            document.getElementById('edit-user-id').value = userId;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-firstname').value = firstname;
            document.getElementById('edit-lastname').value = lastname;
            document.getElementById('edit-admin-modal').style.display = 'flex';
        }
    </script>
</body>

</html>