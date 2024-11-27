<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "../../connection.php"; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id']; 

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

// Handle search functionality
$search_query = '';
$groups = [];
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $sql = "SELECT * FROM study_group WHERE fields_of_interest LIKE ?";
    $search_param = "%" . $search_query . "%";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $search_param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $groups[] = $row;
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error fetching groups: " . mysqli_error($conn);
        exit;
    }
} else {
    // Fetch all groups if no search query is provided
    $sql = "SELECT * FROM study_group";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $groups[] = $row;
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error fetching groups: " . mysqli_error($conn);
        exit;
    }
}

// Handle form submission for joining a group
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['join_group_id'])) {
    $group_id = $_POST['join_group_id'];

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
        echo "Error fetching members: " . mysqli_error($conn);
        exit;
    }

    // Check if the student is already a member
    if (strpos($members, (string)$student_id) !== false) {
        echo "<script>alert('You are already a member of this group.');</script>";
    } else {
        
        $members = $members ? $members . ',' . $student_id : $student_id;

        
        $update_sql = "UPDATE study_group SET members = ? WHERE group_id = ?";
        if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
            mysqli_stmt_bind_param($update_stmt, "si", $members, $group_id);
            if (mysqli_stmt_execute($update_stmt)) {
                header('Location: groups.php?message=Successfully joined the group');
                exit(); 
            } else {
                echo "Error updating group members: " . mysqli_stmt_error($update_stmt);
            }
            mysqli_stmt_close($update_stmt);
        } else {
            echo "Error preparing update query: " . mysqli_error($conn);
        }
    }
 
}
   // Handle form submission for creating a group post
   if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['group_id'])) {
    $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
    $description = mysqli_real_escape_string($conn, $_POST['group_description']);
    $fields_of_interest = mysqli_real_escape_string($conn, $_POST['field']);

   
    $insert_sql = "INSERT INTO study_group (group_name, description, fields_of_interest, created_by) VALUES (?, ?, ?, ?)";
    
    if ($insert_stmt = mysqli_prepare($conn, $insert_sql)) {
        mysqli_stmt_bind_param($insert_stmt, "sssi", $group_name, $description, $fields_of_interest, $student_id);

        if (mysqli_stmt_execute($insert_stmt)) {
           
            header('Location: groups.php?message=Group created successfully');
            exit(); 
        } else {
            echo "Error inserting group post: " . mysqli_stmt_error($insert_stmt);
        }
        mysqli_stmt_close($insert_stmt);
    } else {
        echo "Error preparing group post query: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups</title>
    <style type="text/css">
        <?php include '../../styles/group.css'; ?>
    </style>
</head>

<body>
    <?php include("components/navbar.php"); ?>

    <div class="container">
        

        <div class="tabs">
            <button class="tab-btn active" onclick="showTab('all_groups', this)">All Groups</button>
            <button class="add-group-btn" onclick="showModal()"><i class="fa-solid fa-plus"></i></button>
        </div>
        
        <!-- search bar -->
        <div class="search-bar-container">
        <form action="groups.php" method="GET" class="search-form">
            <input 
                type="text" 
                name="search" 
                value="<?php echo htmlspecialchars($search_query); ?>" 
                placeholder="Search by field of interest..." 
                class="search-input"
            >
            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>

        <!-- All Groups Tab -->
        <div class="tab-content active" id="all_groups">
            <h2>All Groups</h2>

            <!-- Display all groups -->
            <div class="group-list">
                <?php if (!empty($groups)): ?>
                    <?php foreach ($groups as $group): ?>
                        <div class="group-card">
                            <div class="group-header">
                                <h3><?php echo htmlspecialchars($group['group_name']); ?></h3>
                                <form action="groups.php" method="POST">
                                    <input type="hidden" name="join_group_id" value="<?php echo $group['group_id']; ?>">
                                    <button type="submit" class="join-group-btn">Join Group</button>
                                </form>
                            </div>
                            <div class="group-body">
                                <p><?php echo nl2br(htmlspecialchars($group['description'])); ?></p>
                                <p><?php echo htmlspecialchars($group['fields_of_interest']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No groups found matching your search criteria.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal Form for creating a new group -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="hideModal()">&times;</span>
                <form action="groups.php" method="POST">
                    <label for="group_name">Group Name:</label>
                    <input type="text" id="group_name" name="group_name" class="form-control" required>

                    <label for="group_description">Group Description:</label>
                    <textarea id="group_description" name="group_description" class="form-control" required></textarea>

                    <label for="field">Fields of Interest:</label>
                    <input type="text" id="field" name="field" class="form-control" required>

                    <button type="submit" class="btn-submit">Create Group</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../../scripts/group.js"></script>
</body>

</html>
