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

// chat function
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Attempt to include the connection file

$messages = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    if (isset($_POST['group_id']) && isset($_SESSION['user_id'])) {
        $group_id = $_POST['group_id'];
        $user_id = $_SESSION['user_id'];
        $message = mysqli_real_escape_string($conn, $_POST['message']);

        $check_sql = "SELECT COUNT(*) FROM study_group WHERE group_id = ?";
        if ($check_stmt = mysqli_prepare($conn, $check_sql)) {
            mysqli_stmt_bind_param($check_stmt, "i", $group_id);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_bind_result($check_stmt, $count);
            mysqli_stmt_fetch($check_stmt);
            mysqli_stmt_close($check_stmt);

            if ($count > 0) { // Group exists
                $sql = "INSERT INTO group_chat (group_id, user_id, message) VALUES (?, ?, ?)";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "iis", $group_id, $user_id, $message);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    error_log("SQL prepare failed: " . mysqli_error($conn));
                }
            } else {
                error_log("Invalid group_id: " . $group_id);
            }
        } else {
            error_log("SQL prepare failed: " . mysqli_error($conn));
        }
    } else {
        error_log("group_id or user_id not set.");
    }
}

if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];
    $sql = "SELECT gc.message, u.firstname, u.lastname, gc.created_at 
            FROM group_chat gc 
            JOIN users u ON gc.user_id = u.user_id 
            WHERE gc.group_id = ? 
            ORDER BY gc.created_at ASC";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $group_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            error_log("No messages found for group_id: " . $group_id);
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("SQL prepare failed: " . mysqli_error($conn));
    }
} else {
    error_log("group_id not set in GET request.");
}

// debuggin
if (empty($messages)) {
    // echo "No messages to display.";
} else {
    foreach ($messages as $msg) {
        // echo htmlspecialchars($msg['firstname'] . ' ' . $msg['lastname'] . ': ' . $msg['message']) . '<br>';
    }
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
    <div class="main-container">
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
                                <form action="mygroups.php" method="GET" style="display: inline;">
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

        <!-- chat function xx -->
        <div class="chat-container">
            <!-- <h2><?php echo htmlspecialchars($group['group_name']); ?></h2> -->
            <h2>Group Chat</h2>
            <?php
            // Fetch messages for the current group with user details
            $sql = "SELECT gc.id, gc.user_id, gc.message, gc.created_at, u.firstname, u.lastname 
                    FROM group_chat gc 
                    JOIN users u ON gc.user_id = u.user_id 
                    WHERE gc.group_id = ? 
                    ORDER BY gc.created_at ASC";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $group_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                while ($row = mysqli_fetch_assoc($result)) {
                    $messages[] = $row;
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Error fetching messages: " . mysqli_error($conn);
            }
            ?>
            <div id="chat-box" style="height: 300px; overflow-y: auto;">
                <?php foreach ($messages as $msg): ?>
                    <div class="message">
                        <strong><?php echo htmlspecialchars($msg['firstname'] . ' ' . $msg['lastname']); ?>:</strong>
                        <p><?php echo htmlspecialchars($msg['message']); ?></p>
                        <span><?php echo htmlspecialchars($msg['created_at']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <form id="chat-form" method="POST" action="mygroups.php">
                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                <input type="text" name="message" placeholder="Type your message..." required>
                <button type="submit"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>

</body>

</html>