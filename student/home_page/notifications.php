<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

include('../../connection.php');

$user_id = $_SESSION['user_id'];

// Fetch notifications from the Content table
$query_content = "SELECT title, status, created_at 
                  FROM Content 
                  WHERE student_id = (SELECT student_id FROM Student WHERE user_id = ?) 
                  ORDER BY created_at DESC";

$notifications = [];
if ($stmt_content = mysqli_prepare($conn, $query_content)) {
    mysqli_stmt_bind_param($stmt_content, "i", $user_id);
    mysqli_stmt_execute($stmt_content);
    $result_content = mysqli_stmt_get_result($stmt_content);
    while ($row = mysqli_fetch_assoc($result_content)) {
        $notifications[] = [
            'title' => $row['title'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'type' => 'content' // Add type for identification
        ];
    }
    mysqli_stmt_close($stmt_content);
}

// Fetch notifications from the mentors table
$query_mentors = "SELECT subject_area AS title, status, NOW() AS created_at 
                  FROM mentors 
                  WHERE student_id = (SELECT student_id FROM Student WHERE user_id = ?) 
                  ORDER BY created_at DESC";

if ($stmt_mentors = mysqli_prepare($conn, $query_mentors)) {
    mysqli_stmt_bind_param($stmt_mentors, "i", $user_id);
    mysqli_stmt_execute($stmt_mentors);
    $result_mentors = mysqli_stmt_get_result($stmt_mentors);
    while ($row = mysqli_fetch_assoc($result_mentors)) {
        $notifications[] = [
            'title' => "Mentor Post: " . $row['title'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'type' => 'mentor' // Add type for identification
        ];
    }
    mysqli_stmt_close($stmt_mentors);
}

// Sort notifications by created_at in descending order
usort($notifications, function ($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="../../styles/navbar.css"> 
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #002b15;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            font-size: 30px;
            color: #0a3d23;
            margin-bottom: 20px;
        }

        .notifications ul {
            list-style: none;
            padding: 0;
        }

        .notifications li {
            display: flex;
            flex-direction: column;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fafafa;
            transition: background 0.3s, box-shadow 0.3s;
        }

        .notifications li:hover {
            background: #f0f0f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .notification-title {
            font-size: 18px;
            font-weight: bold;
            color: #abda37;
            margin-bottom: 5px;
        }

        .notification-status {
            font-size: 14px;
            font-style: italic;
            color: #666;
        }

        .notification-date {
            margin-top: 10px;
            font-size: 12px;
            color: #999;
            text-align: right;
        }

        .no-notifications {
            text-align: center;
            color: #666;
            font-size: 16px;
            padding: 20px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <?php include("components/navbar.php") ?>

    <div class="container">
        <h1>Your Notifications</h1>
        <div class="notifications">
            <?php if (empty($notifications)) : ?>
                <div class="no-notifications">
                    <p>You have no notifications at this time.</p>
                </div>
            <?php else : ?>
                <ul>
                    <?php foreach ($notifications as $notification) : ?>
                        <li>
                            <span class="notification-title">
                                <?= htmlspecialchars($notification['title']) ?>
                            </span>
                            <span class="notification-status">
                                Status: <?= htmlspecialchars($notification['status']) ?>
                            </span>
                            <?php if ($notification['type'] !== 'mentor') : ?>
                                <span class="notification-date">
                                    Submitted on: <?= date('F j, Y, g:i a', strtotime($notification['created_at'])) ?>
                                </span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
