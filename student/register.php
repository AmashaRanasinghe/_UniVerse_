<?php
include "../connection.php";

if (isset($_POST['register'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $year_of_study = $_POST['year_of_study'];
    $university = $_POST['university'];
    $degree = $_POST['degree'];
    $bio = $_POST['bio'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if ($university === 'Other') {
        $other_university = $_POST['other_university'];
        if (!empty($other_university)) {
            $university = $other_university; 
        } else {
            $error_message = "Please specify your university.";
        }
    }

    if (!preg_match('/^(?=.*[a-zA-Z])(?=.*[\W]).{5,}$/', $password)) {
        $error_message = "Password must be at least 5 characters long, contain at least one letter, and one special character.";
    } elseif ($password !== $cpassword) {
        $error_message = "Passwords do not match.";
    } else {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $profile_picture = '';
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $upload_dir = '../uploads/';
            $file_name = basename($_FILES['file']['name']);
            $upload_file = $upload_dir . $file_name;

            if (getimagesize($_FILES['file']['tmp_name'])) {
                move_uploaded_file($_FILES['file']['tmp_name'], $upload_file);
                $profile_picture = $upload_file;
            } else {
                $error_message = "Uploaded file is not an image.";
            }
        }

        if (!isset($error_message)) {
            $insert_user_query = "INSERT INTO users (email, password_hash, role, firstname, lastname) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_user_query);
            $role = 'Student';
            $stmt->bind_param("sssss", $email, $password_hash, $role, $fname, $lname);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;

                $insert_student_query = "INSERT INTO student (user_id, university, year_of_study, degree_program, bio, profile_picture) 
                                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_student_query);
                $stmt->bind_param("isisss", $user_id, $university, $year_of_study, $degree, $bio, $profile_picture);

                if ($stmt->execute()) {
                    $success_message = "Registration successful! Redirecting to login...";
                    echo "<script>
                        alert('$success_message');
                        window.location.href = 'login.php';
                    </script>";
                    exit();
                } else {
                    $error_message = "Failed to insert student data.";
                }
            } else {
                $error_message = "Failed to register user.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
        <style>
            body {
                background-color: #f4f4f4;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100%;
                margin: 0;
            }

            .wrapper {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: row;
                width: 80%;
                height: auto;
                background-color: #ffffff;
                border-radius: 20px;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                margin: 2%;
            }

            .container {
                padding: 20px;
                color: #ffffff;
                background-color: #004225;
                height: fit-content;
                width: 60%;
            }

            .header {
                text-align: center;
                font-size: 30px;
                font-weight: bold;
                margin-bottom: 3rem;
            }

            form {
                display: flex;
                flex-direction: column;
                border-radius: 20px;
            }

            .form-outline {
                display: flex;
                justify-content: center; 
                margin-bottom: 15px;
            }

            .form-control {
                width: 100%; 
                padding: 10px;
                font-size: 14px;
                border: 1px solid #ddd;
                border-radius: 5px;
                box-sizing: border-box;
            }

            .form-row {
                display: flex;
                justify-content: space-between; 
                align-items: center;
                width: 100%;
                margin: 0 0 1.5rem 0;
            }

            .form-row .form-outline {
                width: 32%; 
            }

            .form-row1{
                display: flex;
                justify-content: space-between; 
                align-items: center;
                width: 100%;
                margin: 0 0 1.5rem 0;
            }

            .form-row1 .form-outline {
                width: 45%; 
            }

            .form-outline select, .form-outline input {
                width: 100%; 
            }


            .btn-primary {
                color: #004225;
                background-color: #ffffff;
                border: 1px solid #004225;
                padding: 10px 20px;
                font-size: 16px;
                border-radius: 5px;
                cursor: pointer;
            }

            .btn-primary:hover {
                background-color: #004225;
                color: white;
            }

            .login {
                text-align: center;
                margin-top: 1rem;
                font-size: 14px;
                color: #ddd;
            }

            .login a {
                text-decoration: none;
                color: #ffffff;
                font-weight: bold;
            }

            .image-container {
                flex: 1;
                width: 40%;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .image-container img {
                max-width: 100%;
                height: auto;
                border-radius: 10px;
            }

            .notification {
                position: fixed;
                top: 10px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #f44336;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                display: none;
                font-size: 16px;
            }

            .notification.success {
                background-color: #4CAF50;
            }

            .notification.error {
                background-color: #f44336;
            }

            .centered-label {
                display: block;
                text-align: center;
                margin-bottom: 0.5rem;
                font-weight: bold;
                color: #ffffff;
            }
        </style>
    </head>
    <body>
        <div id="notification" class="notification"></div>
        <div class="wrapper">
            <div class="container">
                <div class="header">
                    Create Your Account
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <!-- First Name and Last Name Row -->
                    <div class="form-row1">
                        <div class="form-outline">
                            <input type="text" name="fname" class="form-control" placeholder="First Name" required />
                        </div>
                        <div class="form-outline">
                            <input type="text" name="lname" class="form-control" placeholder="Last Name" required />
                        </div>
                    </div>

                    <div class="form-outline">
                        <input type="email" name="email" class="form-control" placeholder="Email" required />
                    </div>
            
                    <div class="form-outline">
                        <label for="file" class="centered-label">Add Profile Picture</label><br><br><br>
                        <input type="file" id="file" name="file" class="form-control" />
                    </div>

                    <div class="form-row">
                        <div class="form-outline">
                            <select name="year_of_study" class="form-control" required>
                                <option value="" disabled selected>Year of Study</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                                <option value="5">5th Year</option>
                            </select>
                        </div>
                        <div class="form-outline">
                            <select name="university" id="university" class="form-control" required>
                                <option value="" disabled selected>Select your university</option>
                                <option value="ICBT">ICBT</option>
                                <option value="IIT">IIT</option>
                                <option value="NSBM">NSBM</option>
                                <option value="NIBM">NIBM</option>
                                <option value="SLIIT">SLIIT</option>
                                <option value="University of Colombo">University of Colombo</option>
                                <option value="University of Jaffna">University of Jaffna</option>
                                <option value="University of Kelaniya">University of Kelaniya</option>
                                <option value="University of Moratuwa">University of Moratuwa</option>
                                <option value="University of Peradeniya">University of Peradeniya</option>
                                <option value="University of Sabaragamuwa">University of Sabaragamuwa</option>
                                <option value="University of Sri Jayawardhanapura">University of Sri Jayawardhanapura</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-outline" id="other-university-container" style="display: none; ">
                            <input type="text" name="other_university" id="other-university" class="form-control" placeholder="Enter your university" />
                        </div>
                    </div>

                    <div class="form-outline">
                        <input type="text" name="degree" class="form-control" placeholder="Degree Program" required />
                    </div>

                    <div class="form-outline">
                        <input type="text" name="bio" class="form-control" placeholder="Bio" />
                    </div>

                    <div class="form-outline">
                        <input type="password" name="password" class="form-control" placeholder="Password" required />
                    </div>

                    <div class="form-outline">
                        <input type="password" name="cpassword" class="form-control" placeholder="Confirm Password" required />
                    </div>

                    <div class="form-outline">
                        <button name="register" class="btn-primary" type="submit">Register</button>
                    </div>

                    <div class="login">
                        <p>Already have an account? <a href="./login.php">Login here</a></p>
                    </div>
                </form>
            </div>

            <div class="image-container">
                <img src="../images/pic.png" alt="Sample photo" />
            </div>
        </div>

        <script>

            document.querySelector('form').addEventListener('submit', (e) => {
                const password = document.querySelector('input[name="password"]').value;
                const cpassword = document.querySelector('input[name="cpassword"]').value;

                const passwordRegex = /^(?=.*[a-zA-Z])(?=.*[\W]).{5,}$/;

                if (!passwordRegex.test(password)) {
                    e.preventDefault();
                    alert("Password must be at least 5 characters long, contain at least one letter, and one special character.");
                } else if (password !== cpassword) {
                    e.preventDefault();
                    alert("Passwords do not match.");
                }
            });


            document.addEventListener("DOMContentLoaded", () => {
                const errorMessage = "<?php echo isset($error_message) ? addslashes($error_message) : ''; ?>";
                const successMessage = "<?php echo isset($success_message) ? addslashes($success_message) : ''; ?>";
                const notificationDiv = document.getElementById("notification");

                if (errorMessage) {
                    notificationDiv.textContent = errorMessage;
                    notificationDiv.classList.add("error");
                    notificationDiv.style.display = "block";
                }

                if (successMessage) {
                    notificationDiv.textContent = successMessage;
                    notificationDiv.classList.add("success");
                    notificationDiv.style.display = "block";
                }

                setTimeout(() => {
                    notificationDiv.style.display = "none";
                }, 5000);
            });

            document.getElementById('university').addEventListener('change', function() {
                var universitySelect = this.value;
                var otherUniversityContainer = document.getElementById('other-university-container');

                if (universitySelect === 'Other') {
                    otherUniversityContainer.style.display = 'block';
                } else {
                    otherUniversityContainer.style.display = 'none';
                }
            });
        </script>
    </body>
</html>