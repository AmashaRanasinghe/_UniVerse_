<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Not connected";
    exit;
}

$user_id = $_SESSION['user_id'];
include('../../connection.php');

// Update status 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id']) && isset($_POST['status'])) {
    $post_id = mysqli_real_escape_string($conn, $_POST['post_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $sql = "UPDATE mentors SET status = ? WHERE mentor_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $status, $post_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "Status updated successfully.";
        } else {
            echo "Error updating status: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

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

// Handle form submission for creating a post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['post_id'])) {
    $subject_area = mysqli_real_escape_string($conn, $_POST['subject']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $status = "Inactive";

    $insert_sql = "INSERT INTO mentors (student_id, subject_area, status, price) VALUES (?, ?, ?, ?)";
    if ($insert_stmt = mysqli_prepare($conn, $insert_sql)) {
        mysqli_stmt_bind_param($insert_stmt, "isss", $student_id, $subject_area, $status, $price);

        if (mysqli_stmt_execute($insert_stmt)) {
            // Redirect to mentorships.php after successful submission
            header("Location: mentorships.php");
            exit;  // Make sure to exit after header redirect to prevent further code execution
        } else {
            echo "Error inserting mentor post: " . mysqli_stmt_error($insert_stmt);
        }
        mysqli_stmt_close($insert_stmt);
    } else {
        echo "Error preparing mentor post query: " . mysqli_error($conn);
    }
}

// Fetch mentor posts for "All Posts" tab (status = Active)
$all_posts = [];
$sql = "SELECT * FROM mentors WHERE status = 'Active'";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $all_posts[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Fetch the mentor posts for the logged-in user
$sql = "SELECT * FROM mentors WHERE student_id = ?";
$mentor_posts = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $mentor_posts[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Post</title>
    <link rel="stylesheet" href="../../styles/mentor.css">
</head>

<body>
    <?php include("components/navbar.php") ?>

    <div class="tab-container">
        <!-- Tab buttons -->
        <div class="tab-buttons">
            <button class="tab-btn active" data-tab="all-posts">Find Mentors</button>
            <button class="tab-btn" data-tab="my-posts">Mentor Now</button>
        </div>

        <!-- Tab content -->
        <div class="tab-content active" id="all-posts">

            <div class="tab-content active" id="all-posts">


                <!-- Mentor Posts List in Card Format -->
                <div class="mentor-posts">
                    <?php foreach ($all_posts as $post): ?>
                        <div class="mentor-post">
                            <div class="mentor-card">
                                <h3><?php echo htmlspecialchars($post['subject_area']); ?></h3>
                                <p><strong>Price:</strong> LKR <?php echo number_format(htmlspecialchars($post['price']), 2); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($post['status']); ?></p>

                                <!-- Request Button -->
                                <button class="request-button">Request</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Mentor Posts List in Card Format -->
            <div class="mentor-posts">
                <?php foreach ($all_posts as $post): ?>
                    <div class="mentor-post">
                        <div class="mentor-card">
                            <h3><?php echo htmlspecialchars($post['subject_area']); ?></h3>
                            <p><strong>Price per hour:</strong> LKR <?php echo number_format(htmlspecialchars($post['price']), 2); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($post['status']); ?></p>

                            <?php

                            $mentor_id = $post['mentor_id'];

                            $sql = "SELECT student_id FROM mentors WHERE mentor_id = ?";
                            $mentor_student_id = 0;
                            if ($stmt = mysqli_prepare($conn, $sql)) {
                                mysqli_stmt_bind_param($stmt, "i", $mentor_id);
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_bind_result($stmt, $mentor_student_id);
                                mysqli_stmt_fetch($stmt);
                                mysqli_stmt_close($stmt);
                            }

                            $user_id = 0;
                            $sql = "SELECT user_id FROM student WHERE student_id = ?";
                            if ($stmt = mysqli_prepare($conn, $sql)) {
                                mysqli_stmt_bind_param($stmt, "i", $mentor_student_id);
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_bind_result($stmt, $user_id);
                                mysqli_stmt_fetch($stmt);
                                mysqli_stmt_close($stmt);
                            }

                            $mentor_email = '';
                            $sql = "SELECT email FROM users WHERE user_id = ?";
                            if ($stmt = mysqli_prepare($conn, $sql)) {
                                mysqli_stmt_bind_param($stmt, "i", $user_id);
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_bind_result($stmt, $mentor_email);
                                mysqli_stmt_fetch($stmt);
                                mysqli_stmt_close($stmt);
                            }
                            ?>

                            <a href="mailto:<?php echo $mentor_email; ?>?subject=Request for Mentorship in <?php echo urlencode($post['subject_area']); ?>&body=Dear Mentor,%0D%0A%0D%0AI would like to request mentorship in the field of <?php echo urlencode($post['subject_area']); ?>.%0D%0A%0D%0AThank you." class="request-button">Request</a>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content" id="my-posts">

            <!-- Create Post Button -->
            <button class="mentor-button" id="mentorButton">Add Post</button>

            <!-- Popup form -->
            <div class="modal" id="mentorModal">
                <div class="modal-content">
                    <span class="close-btn" id="closeModal">&times;</span>
                    <h2>Create Mentor Post</h2><br>

                    <form id="mentorForm" method="POST" action="">
                        <label for="subject">Subject area you will cover?</label>
                        <textarea id="subject" name="subject" placeholder="Describe the subject area" required></textarea>

                        <label for="price">Price per hour</label>
                        <input type="number" id="price" name="price" placeholder="Enter price" required>

                        <button type="submit" class="submit-btn">Submit</button>
                    </form>
                </div>
            </div>

            <!-- Mentor Posts List in Card Format -->
            <div class="mentor-posts">
                <?php foreach ($mentor_posts as $post): ?>
                    <div class="mentor-post">
                        <div class="mentor-card">
                            <h3><?php echo htmlspecialchars($post['subject_area']); ?></h3>
                            <p><strong>Price:</strong> LKR <?php echo number_format(htmlspecialchars($post['price']), 2); ?></p>
                            <p><strong>Status:</strong>
                                <select class="status-dropdown" data-post-id="<?php echo $post['mentor_id']; ?>">
                                    <option value="Inactive" <?php echo $post['status'] == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="Active" <?php echo $post['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                                </select>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            const mentorButton = document.getElementById('mentorButton');
            const mentorModal = document.getElementById('mentorModal');
            const closeModal = document.getElementById('closeModal');

            // Handle tab switching
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    button.classList.add('active');
                    const tabId = button.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });

            // Show modal on 'Create Post' button click
            mentorButton.addEventListener('click', () => {
                mentorModal.style.display = 'block';
            });

            // Close modal
            closeModal.addEventListener('click', () => {
                mentorModal.style.display = 'none';
            });

            // Close modal when clicking outside of it
            window.addEventListener('click', (event) => {
                if (event.target === mentorModal) {
                    mentorModal.style.display = 'none';
                }
            });

            // Handle status change
            const statusDropdowns = document.querySelectorAll('.status-dropdown');
            statusDropdowns.forEach(dropdown => {
                dropdown.addEventListener('change', (event) => {
                    const postId = event.target.getAttribute('data-post-id');
                    const newStatus = event.target.value;

                    fetch('', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `post_id=${postId}&status=${newStatus}`
                        })
                        .then(response => response.text())
                        .then(data => {

                            alert('Status updated successfully!');
                            location.reload();
                        });
                });
            });
        });
    </script>
</body>

</html>