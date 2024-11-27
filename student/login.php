<?php
include "../connection.php"; 
session_start(); 

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);  
        // Execute the prepared statement
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['email'] = $row['email'];

                header('Location: ./home_page/notes.php');
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "No account found with that email.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Database query failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 90%;
            height: 90%;
            border-radius: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background-color: #ffffff;
        }

        .container {
            min-width: 50%;
            min-height: 100%;
            padding: 1rem;
            background-color: #004225;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .header {
            text-align: center;
            font-size: 50px;
            font-weight: 400;
            margin-bottom: 3rem;
        }

        .form-outline {
            margin-bottom: 1.5rem;
        }

        form{ 
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .form-control {
            width: 100%;
            padding: 10px;
        }

        .form-control:focus {
            box-shadow: 0px 0px 5px #d9f99d;
            border-color: #d9f99d;
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
        .create {
            text-align: center;
            margin-top: 2rem;
            color: #C7C7C7;
        }

        .create a {
            text-decoration: none;
            color: #ffffff;
        }

        .image-container {
            max-width: 50%;
            background-color: #f4f4f4;
        }

        .image-container img {
            width: 100%;
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

    </style>
</head>

<body>
    <div id="notification" class="notification"></div>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <p>Welcome Back!</p>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-outline">
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email address" autocomplete="off" required />
                </div>

                <div class="form-outline">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required />
                </div>

                <div class="login_button">
                    <button name="login" class="btn-primary" type="submit">Login</button>
                </div>

                <div class="create">
                    <p class="mb-0 me-2">Don't have an account? <a href="./register.php">Create new</a></p>
                </div>
            </form>
        </div>
    
    

        <div class="col-lg-6 image-container">
            <img src="../images/5186391.jpg" alt="Sample photo" />
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const errorMessage = "<?php echo isset($error_message) ? addslashes($error_message) : ''; ?>";
            const notificationDiv = document.getElementById("notification");

            if (errorMessage) {
                notificationDiv.textContent = errorMessage;
                notificationDiv.classList.add("error");
                notificationDiv.style.display = "block";

                setTimeout(() => {
                    notificationDiv.style.display = "none";
                }, 5000); 
            }
        });
    </script>
</body>
</html>
