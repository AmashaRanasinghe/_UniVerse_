<?php
session_start();
include('../../connection.php');

// err
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mentor_id = $_POST['ment_id'];
    $action = $_POST['action'];

    switch ($action) {
        case 'accept':
            $status = 'Active';
            break;
        case 'reject':
            $status = 'Inactive';
            break;
        default:
            header("Location: ../../secondary_admins/sa_content.php");
            exit();
    }

    $sql = "UPDATE `mentors` SET `status` = ? WHERE `mentor_id` = ?";
    $statement = $conn->prepare($sql);
    $statement->bind_param("si", $status, $mentor_id);

    if ($statement->execute()) {
        if ($action == 'reject') {
            echo "<script>
            alert('Content Rejected!');
            window.location.href = '../sa_mentorships.php';
            </script>";
        } else {
            echo "<script>
            alert('Content Approved!');
            window.location.href = '../sa_mentorships.php';
            </script>";
        }
    } else {
        header("Location: ../admin/content.php?error=update_failed");
    }

    $statement->close();
}

$conn->close();
