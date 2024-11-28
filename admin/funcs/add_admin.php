<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../connection.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $role = 'Secondary Administrator';

    $sql = "INSERT INTO users (email, password_hash, role, created_at, updated_at, firstname, lastname) 
            VALUES (?, ?, ?, NOW(), NOW(), ?, ?)";

    $statement = $conn->prepare($sql);

    if (!$statement) {
        die("Error preparing statement: " . $conn->error);
    }

    $statement->bind_param("sssss", $email, $hashed_password, $role, $firstname, $lastname);

    if ($statement->execute()) {
        $new_user_id = $conn->insert_id;
        $_SESSION['second_admin_id'] = $new_user_id;
        $_SESSION[$role] = $user_role;

        echo "<script>alert('Admin Added Successfully! User ID: " . $new_user_id . "');</script>";
        echo "<script>window.location.href = '../../admin/users.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error Adding Admin!');</script>";
        echo "<script>window.location.href = '../../admin/users.php';</script>";
        exit();
    }
}

$conn->close();
