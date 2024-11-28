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

// Fetch both user details and student details for the logged-in user
$user_details = [];
$student_details = [];

if ($stmt = mysqli_prepare($conn, "SELECT user_id, email, firstname, lastname FROM users WHERE user_id = ?")) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_details['id'], $user_details['email'], $user_details['firstname'], $user_details['lastname']);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    echo "Error fetching user details: " . mysqli_error($conn);
    exit;
}

// Fetch student details from the student table
if ($stmt = mysqli_prepare($conn, "SELECT university, year_of_study, degree_program, bio FROM student WHERE user_id = ?")) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $student_details['university'], $student_details['year_of_study'], $student_details['degree_program'], $student_details['bio']);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    echo "Error fetching student details: " . mysqli_error($conn);
    exit;
}

if (isset($_POST['update'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $university = trim($_POST['university']);
    $year_of_study = (int) $_POST['year_of_study'];
    $degree = trim($_POST['degree']);
    $bio = trim($_POST['bio']);

    if ($university === 'Other') {
        $other_university = trim($_POST['other_university']);
        if (!empty($other_university)) {
            $university = $other_university;
        } else {
            $error_message = "Please specify your university.";
        }
    }

    if (!isset($error_message)) {
        // Update users table
        $update_users_query = "UPDATE users SET email = ?, firstname = ?, lastname = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_users_query);
        $stmt->bind_param("sssi", $email, $fname, $lname, $user_id);
        $users_updated = $stmt->execute();

        // Update student table
        $update_student_query = "UPDATE student SET university = ?, year_of_study = ?, degree_program = ?, bio = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_student_query);
        $stmt->bind_param("sissi", $university, $year_of_study, $degree, $bio, $user_id);
        $student_updated = $stmt->execute();

        if ($users_updated && $student_updated) {
            $success_message = "Profile updated successfully!";
        } else {
            $error_message = "Failed to update profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style type="text/css">
        <?php include '../../styles/updateprofile.css'; ?>
    </style>
</head>
<body>
<div class="update-profile-container">
    <h2>Update Your Profile</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert error"><?php echo $error_message; ?></div>
    <?php elseif (isset($success_message)): ?>
        <div class="alert success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="fname">First Name</label>
            <input type="text" id="fname" name="fname" value="<?php echo htmlspecialchars($user_details['firstname']); ?>" required>
        </div>

        <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($user_details['lastname']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_details['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="university">University</label>
            <select id="university" name="university" required>
                <option value="ICBT" <?php echo $student_details['university'] === 'ICBT' ? 'selected' : ''; ?>>ICBT</option>
                <option value="Other" <?php echo $student_details['university'] === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
            <input type="text" id="other-university" name="other_university" placeholder="Specify your university" 
                   value="<?php echo $student_details['university'] === 'Other' ? htmlspecialchars($student_details['university']) : ''; ?>" 
                   style="display: <?php echo $student_details['university'] === 'Other' ? 'block' : 'none'; ?>;">
        </div>

        <div class="form-group">
            <label for="year_of_study">Year of Study</label>
            <select id="year_of_study" name="year_of_study" required>
                <option value="1" <?php echo $student_details['year_of_study'] == 1 ? 'selected' : ''; ?>>1st Year</option>
                <option value="2" <?php echo $student_details['year_of_study'] == 2 ? 'selected' : ''; ?>>2nd Year</option>
                <option value="3" <?php echo $student_details['year_of_study'] == 3 ? 'selected' : ''; ?>>3rd Year</option>
            </select>
        </div>

        <div class="form-group">
            <label for="degree">Degree Program</label>
            <input type="text" id="degree" name="degree" value="<?php echo htmlspecialchars($student_details['degree_program']); ?>" required>
        </div>

        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($student_details['bio']); ?></textarea>
        </div>

        <button type="submit" name="update" class="btn">Update Profile</button>
    </form>
    <div class="back-button-container">
    <a href="../home_page/notes.php" class="btn btn-back">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>
</div>
</div>


<script>
    document.getElementById('university').addEventListener('change', function () {
        const otherUniversityInput = document.getElementById('other-university');
        otherUniversityInput.style.display = this.value === 'Other' ? 'block' : 'none';
    });
</script>
</body>
</html>
