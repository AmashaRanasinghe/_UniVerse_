<?php
session_start();
include('../../connection.php');

// err
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content_id = $_POST['content_id'];
    $action = $_POST['action'];

    switch ($action) {
        case 'accept':
            $status = 'Approved';
            break;
        case 'pending':
            $status = 'Pending Approval';
            break;
        case 'reject':
            $status = 'Rejected';
            break;
        default:
            header("Location: ../../secondary_admins/sa_content.php");
            exit();
    }

    $sql = "UPDATE `content` SET `status` = ? WHERE `content_id` = ?";
    $statement = $conn->prepare($sql);
    $statement->bind_param("si", $status, $content_id);

    if ($statement->execute()) {
        if ($action == 'reject') {
            echo "<script>
            alert('Content Rejected!');
            window.location.href = '../sa_content.php';
            </script>";
        } else {
            echo "<script>
            alert('Content Approved!');
            window.location.href = '../sa_content.php';
            </script>";
        }
    } else {
        header("Location: ../admin/content.php?error=update_failed");
    }

    $statement->close();
}

$conn->close();
