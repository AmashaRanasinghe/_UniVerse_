<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "../../connection.php"; // Include the database connection

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user_id from the session

// Fetch the student_id for the logged-in user
$sql = "SELECT student_id FROM student WHERE user_id = ?";
$student_id = 0;
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $student_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    echo "Error fetching student details: " . mysqli_error($conn);
    exit;
}

// Handle Exit Group functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exit_group_id'])) {
    $group_id = $_POST['exit_group_id'];

    // Fetch the current members of the group
    $sql = "SELECT members FROM study_group WHERE group_id = ?";
    $members = '';
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $group_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $members);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo "Error fetching group members: " . mysqli_error($conn);
        exit;
    }

    // Remove the student_id from the members list
    $members_array = explode(',', $members);
    if (($key = array_search($student_id, $members_array)) !== false) {
        unset($members_array[$key]);
    }
    $updated_members = implode(',', $members_array);

    // Update the group with the updated members list
    $update_sql = "UPDATE study_group SET members = ? WHERE group_id = ?";
    if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
        mysqli_stmt_bind_param($update_stmt, "si", $updated_members, $group_id);
        if (mysqli_stmt_execute($update_stmt)) {
            header('Location: mygroups.php?message=You have successfully left the group');
            exit(); // Ensure to exit after the redirect
        } else {
            echo "Error updating group members: " . mysqli_stmt_error($update_stmt);
        }
        mysqli_stmt_close($update_stmt);
    } else {
        echo "Error preparing update query: " . mysqli_error($conn);
    }
}

// Fetch groups where the student_id exists in the members column
$my_groups = [];
$sql = "SELECT * FROM study_group WHERE FIND_IN_SET(?, members)";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $my_groups[] = $row;
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error fetching groups: " . mysqli_error($conn);
    exit;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Groups</title>
    <link rel="stylesheet" href="../../styles/group.css"> <!-- Link your CSS file -->
</head>

<body>
    <?php include("components/navbar.php"); ?>

    <div class="container">
        <h2>My Groups</h2>

        <?php if (!empty($my_groups)): ?>
            <div class="group-list">
                <?php foreach ($my_groups as $group): ?>
                    <div class="group-card">
                        <div class="group-header">
                            <h3><?php echo htmlspecialchars($group['group_name']); ?></h3>
                        </div>
                        <div class="group-body">
                            <p><?php echo nl2br(htmlspecialchars($group['description'])); ?></p>
                            <p><strong>Fields of Interest:</strong> <?php echo htmlspecialchars($group['fields_of_interest']); ?></p>
                        </div>
                        <div class="group-actions">
                            <form action="chat.php" method="GET" style="display: inline;">
                                <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                <button type="submit" class="btn chat-btn">Chat</button>
                            </form>
                            <form action="mygroups.php" method="POST" style="display: inline;">
                                <input type="hidden" name="exit_group_id" value="<?php echo $group['group_id']; ?>">
                                <button type="submit" class="btn exit-btn" onclick="return confirm('Are you sure you want to leave this group?');">Exit</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>You are not a member of any groups yet.</p>
        <?php endif; ?>
    </div>
</body>

</html>
