<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    $stmt = $conn->prepare("UPDATE `users` SET `email` = ?, `firstname` = ?, `lastname` = ? WHERE `user_id` = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssi", $email, $firstname, $lastname, $user_id);

    if ($stmt->execute()) {
        echo "
        <script>alert('Admin Edited Successfully!');
        window.location.href = '../../admin/users.php';
        </script>;
        ";
    } else {
        echo "
        <script>alert('Admin Editing Failed: " . $stmt->error . "');</script>
        window.location.href = '../../admin/users.php';
        ";
    }

    $stmt->close();
    $conn->close();
}
