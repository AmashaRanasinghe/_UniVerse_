<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../../connection.php');

if (isset($_GET['id'])) {
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $userID = intval($_GET['id']);
    $sql = "DELETE FROM users WHERE role = 'Secondary Administrator' AND user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);

    if ($stmt->execute()) {
        echo "<script>
        alert('User Deleted Successfully!');
        window.location.href = '../users.php';
        </script>";
    } else {
        echo "Error Deleting User: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
}
