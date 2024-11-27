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

    $sql = "SELECT s.student_id, u.firstname, u.lastname, s.profile_picture FROM Student s 
            JOIN Users u ON s.user_id = u.user_id WHERE u.user_id = ?";
    $student_id = 0;
    $firstname = '';
    $lastname = '';
    $profile_picture = '';
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $student_id, $firstname, $lastname, $profile_picture);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    // Fetch videos uploaded by all users (for the Explore section)
    $sql = "SELECT c.content_id,c.title, c.description, c.file_url, u.firstname, u.lastname, s.profile_picture 
    FROM Content c
    JOIN Student s ON c.student_id = s.student_id
    JOIN Users u ON s.user_id = u.user_id
    WHERE c.content_type = 'Video'
    AND c.status = 'Approved'";

    $all_videos = [];
    if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $content_id,$title, $description, $file_url, $video_firstname, $video_lastname, $video_profile_picture);

    while (mysqli_stmt_fetch($stmt)) {
    // Generate thumbnail URL (this assumes the video file is stored under 'uploads/videos' and thumbnails are stored in 'uploads/thumbnails')
    $thumbnail_url = 'thumbnails/' . basename($file_url, '.mp4') . '.jpg'; // Assuming the thumbnail is named similarly to the video file

    $all_videos[] = [
        'content_id' => $content_id,
        'title' => $title,
        'description' => $description,
        'file_url' => $file_url,
        'thumbnail_url' => $thumbnail_url,  // Generated dynamically
        'firstname' => $video_firstname,
        'lastname' => $video_lastname,
        'profile_picture' => $video_profile_picture
    ];
    }
    mysqli_stmt_close($stmt);
    }


    if (isset($_POST['add_video'])) {
        include "../../connection.php";

        $video_title = mysqli_real_escape_string($conn, $_POST['video-title']);
        $video_description = mysqli_real_escape_string($conn, $_POST['video-description']);
        $file_url = '';

        if (isset($_FILES['video-file']) && $_FILES['video-file']['error'] == 0) {
            $file_name = $_FILES['video-file']['name'];
            $file_tmp = $_FILES['video-file']['tmp_name'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_url = '../../uploads/' . uniqid() . '.' . $file_ext;

            // Move the uploaded file
            if (!move_uploaded_file($file_tmp, $file_url)) {
                echo "Error uploading file.";
                exit();
            }

            // Generate a thumbnail using FFmpeg (extract first second of the video)
            $thumbnail_url = '../../uploads/thumbnails/' . uniqid() . '.jpg';
            $command = "ffmpeg -i $file_url -ss 00:00:01 -vframes 1 $thumbnail_url";
            exec($command);  // Execute FFmpeg command to generate the thumbnail
        }

        if (!empty($video_title) && !empty($video_description)) {
            $sql = "INSERT INTO Content (student_id, content_type, title, description, file_url) VALUES (?, 'Video', ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "isss", $student_id, $video_title, $video_description, $file_url);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }

    // Fetch the videos uploaded by the current user
    $sql = "SELECT content_id, title, description, file_url FROM Content WHERE student_id = ? AND content_type = 'Video'";
    $user_videos = [];
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $student_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$content_id, $title, $description, $file_url);
        
        while (mysqli_stmt_fetch($stmt)) {
            $user_videos[] = ['content_id' => $content_id, 'title' => $title, 'description' => $description, 'file_url' => $file_url];
        }
        mysqli_stmt_close($stmt);
    }

    if (isset($_POST['report'])) {
        $content_id = $_POST['content_id'];
        $report_reason = mysqli_real_escape_string($conn, $_POST['report_reason']);
        $sql = "INSERT INTO Report (content_id, reported_by, report_reason, status) 
                VALUES (?, ?, ?, 'Under Review')";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "iis", $content_id, $student_id, $report_reason);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "Video reported successfully.";
        }
        exit();
    }

    if (isset($_POST['delete_comment'])) {
        $comment_id = $_POST['comment_id'];

        $sql = "SELECT student_id FROM Comments WHERE comment_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $comment_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $comment_student_id);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if ($comment_student_id == $student_id) {
                $sql = "DELETE FROM Comments WHERE comment_id = ?";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "i", $comment_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
        header("Location: videos.php");
        exit();
    }

    if (isset($_POST['like'])) {
        $content_id = $_POST['content_id'];
        
        $sql = "SELECT * FROM Likes WHERE content_id = ? AND student_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $content_id, $student_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0) {
                $sql = "INSERT INTO Likes (content_id, student_id) VALUES (?, ?)";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ii", $content_id, $student_id);
                    mysqli_stmt_execute($stmt);
                }
            } else {
                $sql = "DELETE FROM Likes WHERE content_id = ? AND student_id = ?";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ii", $content_id, $student_id);
                    mysqli_stmt_execute($stmt);
                }
            }
            mysqli_stmt_close($stmt);
        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    if (isset($_POST['toggle_collection'])) {
        $content_id = $_POST['content_id'];
        
        $sql = "SELECT * FROM Collection WHERE content_id = ? AND student_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $content_id, $student_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0) {
                $sql = "INSERT INTO Collection (student_id, content_id) VALUES (?, ?)";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ii", $student_id, $content_id);
                    mysqli_stmt_execute($stmt);
                    echo json_encode(['status' => 'added']); 
                }
            } else {
                $sql = "DELETE FROM Collection WHERE content_id = ? AND student_id = ?";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ii", $content_id, $student_id);
                    mysqli_stmt_execute($stmt);
                    echo json_encode(['status' => 'removed']); 
                }
            }
            mysqli_stmt_close($stmt);
        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit(); 
    }

    if (isset($_GET['fetch_comments'])) {
        $content_id = $_GET['fetch_comments'];
        
        $sql_student = "
            SELECT student_id 
            FROM Student 
            WHERE user_id = ?
        ";
        
        $student_id = null;
        if ($stmt_student = mysqli_prepare($conn, $sql_student)) {
            mysqli_stmt_bind_param($stmt_student, "i", $user_id);
            mysqli_stmt_execute($stmt_student);
            mysqli_stmt_bind_result($stmt_student, $student_id);
            mysqli_stmt_fetch($stmt_student);
            mysqli_stmt_close($stmt_student);
        }

        if ($student_id === null) {
            echo "Student ID not found.";
            exit();
        }
        
        $sql = "
            SELECT c.comment_id, c.text, c.created_at, u.firstname, u.lastname, c.student_id
            FROM Comments c
            JOIN Student s ON c.student_id = s.student_id
            JOIN Users u ON s.user_id = u.user_id
            WHERE c.content_id = ?
            ORDER BY c.created_at ASC
        ";
        
        $comments = [];
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind parameters: content_id
            mysqli_stmt_bind_param($stmt, "i", $content_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            while ($row = mysqli_fetch_assoc($result)) {
                $comments[] = $row;
            }
            mysqli_stmt_close($stmt);
        }
        foreach ($comments as $comment) {
            echo '<div class="comment-details">';
            echo '<div class="comment-author">';
            echo '<strong>' . htmlspecialchars($comment['firstname'] . ' ' . $comment['lastname']);
            echo '</div>';
            echo '<div class="comment">';
            echo htmlspecialchars($comment['text']);
            if ($comment['student_id'] == $student_id) {
                echo ' <button onclick="deleteComment(' . $comment['comment_id'] . ')"><i class="fa-regular fa-trash-can"></i></button>';
            }
            echo '</div>';
            echo '</div>';
        }
        
        exit();
    }


    if (isset($_POST['new_comment']) && isset($_POST['content_id'])) {
        $text = mysqli_real_escape_string($conn, $_POST['new_comment']);
        $content_id = $_POST['content_id'];

        if (!empty($text)) {
            $sql = "INSERT INTO Comments (content_id, student_id, text) VALUES (?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "iis", $content_id, $student_id, $text);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        header("Location: videos.php");
        exit();
    }
        

    if (isset($_GET['delete'])) {
        // Sanitize the input
        $content_id = intval($_GET['delete']); // Ensures it is an integer

        // Check if the content exists in the database
        $check_sql = "SELECT * FROM Content WHERE content_id = ?";
        if ($stmt = mysqli_prepare($conn, $check_sql)) {
            mysqli_stmt_bind_param($stmt, "i", $content_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                // Proceed with deletion
                $delete_sql = "DELETE FROM Content WHERE content_id = ?";
                if ($delete_stmt = mysqli_prepare($conn, $delete_sql)) {
                    mysqli_stmt_bind_param($delete_stmt, "i", $content_id);
                    if (mysqli_stmt_execute($delete_stmt)) {
                        // Successfully deleted
                        echo "<script>
                            alert('Video deleted successfully.');
                            window.location.href = 'videos.php'; 
                        </script>";
                    } else {
                        echo "<script>
                            alert('Failed to delete the video. Please try again.');
                            window.history.back();
                        </script>";
                    }
                    mysqli_stmt_close($delete_stmt);
                }
            } else {
                // Post not found
                echo "<script>
                    alert('Video not found.');
                    window.history.back();
                </script>";
            }
            mysqli_stmt_close($stmt);
        } else {
            // Query preparation failed
            echo "<script>
                alert('An error occurred. Please try again.');
                window.history.back();
            </script>";
        }
    }

    // Handle search functionality
    $search_query = '';
    $all_videos = [];
    $sql = "SELECT c.content_id, c.title, c.description, c.file_url, u.firstname, u.lastname, s.profile_picture
        FROM Content c
        JOIN Student s ON c.student_id = s.student_id
        JOIN Users u ON s.user_id = u.user_id
        WHERE c.content_type = 'Video'
        AND c.status = 'Approved'";

if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $sql .= " AND c.title LIKE ?";


if ($stmt = mysqli_prepare($conn, $sql)) {
    if (!empty($search_query)) {
        $search_param = "%" . $search_query . "%";
        mysqli_stmt_bind_param($stmt, 's', $search_param);
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $content_id, $title, $description, $file_url, $video_firstname, $video_lastname, $video_profile_picture);

    while (mysqli_stmt_fetch($stmt)) {
        $thumbnail_url = 'thumbnails/' . basename($file_url, '.mp4') . '.jpg';

        $all_videos[] = [
            'content_id' => $content_id,
            'title' => $title,
            'description' => $description,
            'file_url' => $file_url,
            'thumbnail_url' => $thumbnail_url,
            'firstname' => $video_firstname,
            'lastname' => $video_lastname,
            'profile_picture' => $video_profile_picture,
        ];
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error fetching videos: " . mysqli_error($conn);
    exit;
}
    } else {
        $sql = "SELECT c.content_id,c.title, c.description, c.file_url, u.firstname, u.lastname, s.profile_picture 
    FROM Content c
    JOIN Student s ON c.student_id = s.student_id
    JOIN Users u ON s.user_id = u.user_id
    WHERE c.content_type = 'Video'
    AND c.status = 'Approved'";

    $all_videos = [];
    if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $content_id,$title, $description, $file_url, $video_firstname, $video_lastname, $video_profile_picture);

    while (mysqli_stmt_fetch($stmt)) {
    // Generate thumbnail URL (this assumes the video file is stored under 'uploads/videos' and thumbnails are stored in 'uploads/thumbnails')
    $thumbnail_url = 'thumbnails/' . basename($file_url, '.mp4') . '.jpg'; // Assuming the thumbnail is named similarly to the video file

    $all_videos[] = [
        'content_id' => $content_id,
        'title' => $title,
        'description' => $description,
        'file_url' => $file_url,
        'thumbnail_url' => $thumbnail_url,  // Generated dynamically
        'firstname' => $video_firstname,
        'lastname' => $video_lastname,
        'profile_picture' => $video_profile_picture
    ];
    }
    mysqli_stmt_close($stmt);
    }
    }


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videos</title>
    <style type="text/css">
            <?php include '../../styles/videos.css'; ?>
    </style>
</head>

<div>
    <?php include "components/navbar.php" ?>

    <div class="container">
    <div class="video">
    <!-- Navigation Tabs -->
    <div class="tabs">
        <button class="tab-link active" onclick="switchTab('explore')">Explore</button>
        <button class="tab-link" onclick="switchTab('my-videos')">My Videos</button>
        <button class="add-video-btn" onclick="showAddVideoModal()"><i class="fa-solid fa-plus"></i></button>
    </div>

    <!-- Comment Modal -->
    <div id="commentModal" class="com-modal">
        <div class="com-modal-content">
            <h2>Comments</h2>
            <br>
            <div id="comments-container">
                
            </div>
            <form id="commentForm" method="POST" action="videos.php">
                <input type="hidden" id="comment_content_id" name="content_id">
                <textarea id="new_comment" name="new_comment" class="form-control" rows="3" placeholder="Add a comment" required></textarea>
                <button type="submit" class="btn-submit">Add Comment</button>
                <button type="button" class="btn-submit" onclick="closeCommentModal()">Close</button>
            </form>
        </div>
    </div>


        <!-- Report Modal -->
        <div id="reportModal" class="com-modal">
            <div class="com-modal-content">
                <h2>Report Video</h2>
                <br>
                <form id="reportForm" method="POST" action="videos.php">
                    <input type="hidden" id="content_id" name="content_id">
                    <textarea id="report_reason" name="report_reason" class="form-control" rows="5" placeholder="Enter your reason for reporting" required></textarea>
                    <button type="submit" class="btn-submit">Submit Report</button>
                    <button type="button" class="btn-submit" onclick="closeReportModal()">Cancel</button>
                </form>
            </div>
        </div>

    <!-- Explore Section -->
    <div id="explore" class="tab-content active">
        <div class="search-bar-container">
            <form action="videos.php" method="GET" class="search-form">
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo htmlspecialchars($search_query); ?>" 
                    placeholder="Search..." 
                    class="search-input"
                >
                <button type="submit" class="search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
        <div class="video-grid" id="video-grid">
            <?php foreach ($all_videos as $video): ?>
                <div class="video-container">
                    <div class="video-thumbnail">
                        <!-- Use the dynamically generated thumbnail for the preview -->
                        <video class="video-preview-thumbnail" poster="../../uploads/thumbnails/<?= $video['thumbnail_url'] ?>" onclick="playVideo(this)">
                            <source src="../../uploads/videos/<?= $video['file_url'] ?>" type="video/mp4">
                        </video>
                    </div>

                    <div class="video-header">
                        <div class="video-info">
                            <img class="profile-img" src="../../uploads/<?= $profile_picture ?>" alt="Profile Picture">
                            <div class="profile-info">
                                <p class="video-title"><?= $video['title'] ?></p>
                                <p class="video-description"><?= $video['description'] ?></p><br>
                                <h4><?= $firstname . ' ' . $lastname ?></h4>
                            </div>        
                        </div>
                        <div class="actions">
                            <!-- Like Button -->
                            <form method="POST" action="videos.php">
                                <button type="submit" name="like" class="like-btn">
                                    <input type="hidden" name="content_id" value="<?php echo $video['content_id']; ?>">
                                    <?php
                                    $liked = false;
                                    $sql = "SELECT * FROM Likes WHERE content_id = ? AND student_id = ?";
                                    if ($stmt = mysqli_prepare($conn, $sql)) {
                                        mysqli_stmt_bind_param($stmt, "ii", $video['content_id'], $student_id);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);
                                        if (mysqli_num_rows($result) > 0) {
                                            $liked = true;
                                        }
                                        mysqli_stmt_close($stmt);
                                    }
                                    ?>
                                    <i class="fa<?php echo $liked ? '-solid' : '-regular'; ?> fa-heart"></i>
                                </button>
                            </form>

                            <!-- Collection Button -->
                            <form method="POST" action="videos.php">
                                <button type="submit" name="toggle_collection" class="bookmark-btn">
                                    <input type="hidden" name="content_id" value="<?php echo $video['content_id']; ?>">

                                    <?php
                                    $is_in_collection = false;
                                    $sql = "SELECT * FROM Collection WHERE content_id = ? AND student_id = ?";
                                    if ($stmt = mysqli_prepare($conn, $sql)) {
                                        mysqli_stmt_bind_param($stmt, "ii", $video['content_id'], $student_id);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);
                                        if (mysqli_num_rows($result) > 0) {
                                            $is_in_collection = true;
                                        }
                                        mysqli_stmt_close($stmt);
                                    }

                                    // Display appropriate bookmark icon based on collection status
                                    if ($is_in_collection) {
                                        echo '<i class="fa-solid fa-bookmark"></i>';
                                    } else {
                                        echo '<i class="fa-regular fa-bookmark"></i>';
                                    }
                                    ?>
                                </button>
                            </form>
                        
                            <form><!-- Comment Button -->
                            <button type="button" onclick="showComments(<?php echo $video['content_id']; ?>)" class="btn-comment">
                                <i class="fa-regular fa-comment"></i>
                            </button>
                            </form>
                            <!-- Report Button -->
                            <form method="POST" action="videos.php">
                                <button type="button" onclick="reportVideo(<?php echo $video['content_id']; ?>)" class="btn-report">
                                    <i class="fa-regular fa-flag"></i> 
                                </button>
                            </form>
                        </div>
                        
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- My Videos Section -->
    <div id="my-videos" class="tab-content">
        <div class="my-videos-grid" id="my-videos-grid">
            <?php foreach ($user_videos as $video): ?>
                <div class="video-container">
                    <div class="video-thumbnail">
                        <!-- Use the dynamically generated thumbnail for the preview -->
                        <video class="video-preview-thumbnail" poster="../../uploads/thumbnails/<?= $video['thumbnail_url'] ?>" onclick="playVideo(this)">
                            <source src="../../uploads/videos/<?= $video['file_url'] ?>" type="video/mp4">
                        </video>
                    </div>

                    <div class="video-header">
                        <div class="video-info">
                        <img class="profile-img" src="../../uploads/<?= $profile_picture ?>" alt="Profile Picture">
                            <div class="profile-info">
                                <p class="video-title"><?= $video['title'] ?></p>
                                <p class="video-description"><?= $video['description'] ?></p>
                                <h4><?= $firstname . ' ' . $lastname ?></h4>
                            </div> 
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    </div>



    <!-- Modal for Adding Videos -->
    <div id="addVideoModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Add a New Video</h2>
            <form id="add-video-form" method="POST" action="videos.php" enctype="multipart/form-data">
                <input type="text" id="video-title" name="video-title" placeholder="Enter video title" required />

                <textarea id="video-description" name="video-description" rows="4" placeholder="Enter video description" required></textarea>

                <input type="file" id="video-file" name="video-file" accept="video/*" required />

                <button type="submit" name="add_video" class="btn-submit">Add Video</button>
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
    
        // Function to switch tabs
        function switchTab(tabName) {
            // Get all tab content elements
            const tabContents = document.querySelectorAll('.tab-content');

            // Hide all tab content
            tabContents.forEach((content) => {
                content.classList.remove('active');
            });

            // Remove active class from all tab links
            const tabLinks = document.querySelectorAll('.tab-link');
            tabLinks.forEach((link) => {
                link.classList.remove('active');
            });

            // Show the selected tab's content and highlight the tab link
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }


            // Show the Add Video Modal
        function showAddVideoModal() {
            document.getElementById("addVideoModal").style.display = "flex";
        }

        // Close the Modal
        function closeModal() {
            document.getElementById("addVideoModal").style.display = "none";
        }

        // Function to play the video when clicked
        function playVideo(videoElement) {
            // Pause any other video that may be playing
            const allVideos = document.querySelectorAll('.video-preview');
            allVideos.forEach((video) => {
                if (video !== videoElement) {
                    video.pause(); // Pause other videos
                }
            });

            // Play the clicked video
            if (videoElement.paused) {
                videoElement.play();
            } else {
                videoElement.pause(); // Pause if already playing (toggle play/pause)
            }
        }

        // Function to play the video on hover
        document.querySelectorAll('.video-preview-thumbnail').forEach(function(videoElement) {
            videoElement.addEventListener('mouseenter', function() {
                // Play the video when hovered over
                videoElement.play();
            });

            videoElement.addEventListener('mouseleave', function() {
                // Pause the video when hover ends
                videoElement.pause();
            });
        });

        document.querySelectorAll('.video-preview-thumbnail').forEach(function(videoElement) {
            videoElement.addEventListener('click', function() {
                const studentId = <?= $student_id ?>;
                const firstname = '<?= $firstname ?>';
                const lastname = '<?= $lastname ?>';
                const profilePicture = '<?= $profile_picture ?>';
                
                playFullScreen(videoElement, studentId, firstname, lastname, profilePicture);
            });
        });


        function playFullScreen(videoElement, studentId, firstname, lastname, profilePicture) {
            const videoSource = videoElement.querySelector('source').src; // Get the video URL from the source

            const fullscreenContainer = document.createElement('div');
            fullscreenContainer.classList.add('fullscreen-video');

            // Create a close button
            const closeBtn = document.createElement('span');
            closeBtn.classList.add('close-btn');
            closeBtn.textContent = '×';
            closeBtn.addEventListener('click', function() {
                document.body.removeChild(fullscreenContainer);  // Remove the fullscreen container
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            });

            // Create a new video element for fullscreen
            const video = document.createElement('video');
            video.setAttribute('controls', 'true');
            video.setAttribute('autoplay', 'true');
            const sourceElement = document.createElement('source');
            sourceElement.setAttribute('src', videoSource); // Use the correct source from the original video
            sourceElement.setAttribute('type', 'video/mp4');
            video.appendChild(sourceElement);

            video.classList.add('fullscreen-video-element');

            fullscreenContainer.appendChild(video);
            fullscreenContainer.appendChild(closeBtn);

            document.body.appendChild(fullscreenContainer);  // Append the fullscreen video container to the body

            // Request fullscreen for the video container
            if (fullscreenContainer.requestFullscreen) {
                fullscreenContainer.requestFullscreen();
            } else if (fullscreenContainer.webkitRequestFullscreen) {
                fullscreenContainer.webkitRequestFullscreen();
            } else if (fullscreenContainer.mozRequestFullScreen) {
                fullscreenContainer.mozRequestFullScreen();
            } else if (fullscreenContainer.msRequestFullscreen) {
                fullscreenContainer.msRequestFullscreen();
            }
        }

        function toggleLike(videoId) {
                var icon = document.getElementById("like-icon-" + videoId);

                // Toggle the class of the heart icon between solid and regular
                if (icon.classList.contains('fa-regular')) {
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');
                } else {
                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-regular');
                }

                // Send AJAX request to toggle the like status
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "videos.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        console.log("Like status updated successfully");
                    } else {
                        console.error("Error updating like status");
                    }
                };
                xhr.send("like=true&content_id=" + videoId);
            }


            function toggleCollection(videoId) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "videos.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText); 

                    var collectionBtn = document.querySelector(`button[name="toggle_collection"][value="${videoId}"]`);

                    if (response.status === 'added') {
                        collectionBtn.innerHTML = '<i class="fa-solid fa-bookmark"></i>';  
                        alert("Video added to collection!"); 
                    } 
                    else if (response.status === 'removed') {
                        collectionBtn.innerHTML = '<i class="fa-regular fa-bookmark"></i>';  
                        alert("Video removed from collection!"); 
                    }
                    } else {
                        console.error("Error toggling collection status");
                    }
                };
                xhr.send("toggle_collection=true&content_id=" + videoId);
            }

            function reportVideo(contentId) {
                document.getElementById("content_id").value = contentId;  
                document.getElementById("reportModal").style.display = "block"; 
            }

            function closeReportModal() {
                document.getElementById("reportModal").style.display = "none"; 
            }

            document.getElementById("reportForm").addEventListener("submit", function(event) {
                event.preventDefault();
                
                var contentId = document.getElementById("content_id").value; 
                var reportReason = document.getElementById("report_reason").value;  

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "videos.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        alert("Video reported successfully.");  
                        closeReportModal(); 
                    } else {
                        alert("Error reporting the video."); 
                    }
                };
                xhr.send("report=true&content_id=" + contentId + "&report_reason=" + encodeURIComponent(reportReason)); 
            });

            function showComments(contentId) {
                document.getElementById("comment_content_id").value = contentId;

                var xhr = new XMLHttpRequest();
                xhr.open("GET", "videos.php?fetch_comments=" + contentId, true);
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        document.getElementById("comments-container").innerHTML = xhr.responseText;
                        document.getElementById("commentModal").style.display = "block";
                    } else {
                        alert("Error fetching comments.");
                    }
                };
                xhr.send();
            }

            function closeCommentModal() {
                document.getElementById("commentModal").style.display = "none";
            }

            function deleteComment(commentId) {
                if (confirm("Are you sure you want to delete this comment?")) {
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "videos.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            alert("Comment deleted successfully.");
                            // Refresh comments
                            var contentId = document.getElementById("comment_content_id").value;
                            showComments(contentId);
                        } else {
                            alert("Error deleting comment.");
                        }
                    };
                    xhr.send("delete_comment=true&comment_id=" + commentId);
                }
            }

        </script>
    </body>
</html>