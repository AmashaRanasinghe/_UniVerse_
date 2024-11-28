<?php
include("../connection.php");
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['login'])) {
  $stmt = $conn->prepare("SELECT * FROM `users` WHERE `email` = ?");
  $stmt->bind_param("s", $_POST['email']);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();

    if (password_verify($_POST['password'], $user['password_hash']) || $_POST['password'] == $user['password_hash']) {
      $username = $_POST['email'];
      $_SESSION['email'] = $username;

      if ($_POST['admin_type'] === "Primary Administrator") {
        echo "<script>alert('Successfully Logged in as Primary Administrator'); 
       window.location.href='../../_UniVerse_/admin/dashboard.php';
        </script>";
        exit();
      } elseif ($_POST['admin_type'] === "Secondary Administrator") {
        echo "<script>alert('Successfully Logged in as Secondary Administrator'); 
        window.location.href='../../_UniVerse_/secondary_admins/sa_dashboard.php';
        </script>";
        exit();
      } else {
        $message = "Invalid admin type!";
        echo "<script>alert('$message');</script>";
      }
    } else {
      error_log("Password verification failed for user: " . $_POST['email']);
      $message = "Login failed! Incorrect password.";
      echo "<script>alert('$message');</script>";
    }
  } else {
    error_log("No user found with email: " . $_POST['email']);
    $message = "Login failed! No user found.";
    echo "<script>alert('$message');</script>";
  }
  $stmt->close();
};

$hashedPassword = password_hash('your_original_password', PASSWORD_DEFAULT);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link rel="stylesheet" href="../styles/admin-login.css" />
</head>

<body>
  <div class="container">
    <h1>Admin Login Page</h1>
    <form action="../admin/login.php" method="POST" id="form-lg" enctype="multipart/form-data">
      <input type="text" name="email" placeholder="Email" required autocomplete="off" />
      <input type="password" name="password" placeholder="Password" required autocomplete="off" />

      <select name="admin_type" required>
        <option value="" disabled selected>Select Admin Type</option>
        <option value="Primary Administrator">Primary Administrator</option>
        <option value="Secondary Administrator">Secondary Administrator</option>
      </select>
      <button type="submit" name="login">Login</button>
    </form>
  </div>
</body>

</html>