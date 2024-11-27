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
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Collections</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style type="text/css">
            <?php include '../../styles/collection.css'; ?>
        </style>
    </head>

    <body>
        <?php include("components/navbar.php"); ?>
        <div class="container">
        <h1>Collections</h1><br><br>
    <div class="tabs">
        <button class="tab-btn active" onclick="loadPage('collection_notes.php')">Notes</button>
        <button class="tab-btn" onclick="loadPage('collection_videos.php')">Videos</button>
        <button class="tab-btn" onclick="loadPage('collection_posts.php')">Posts</button>
    </div>

    <!-- Container for Dynamic Content -->
    <div id="content-container">
    </div>
</div>

<script>
        // Load the page and set active tab
        function loadPage(page, element) {
            // Select the content container
            const container = document.getElementById('content-container');
            
            // Show a loading spinner or message
            container.innerHTML = '<p>Loading...</p>';

            // Perform an AJAX request to load the page
            const xhr = new XMLHttpRequest();
            xhr.open('GET', page, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Update the container with the fetched content
                    container.innerHTML = xhr.responseText;

                    // Update active class on the tabs
                    const tabs = document.querySelectorAll('.tab-btn');
                    tabs.forEach(tab => tab.classList.remove('active'));
                    if (element) element.classList.add('active');
                } else {
                    // Handle errors
                    container.innerHTML = '<p>Error loading content. Please try again.</p>';
                }
            };
            xhr.onerror = function () {
                container.innerHTML = '<p>Failed to connect to the server. Check your connection.</p>';
            };
            xhr.send();
        }

        // Load notes content by default when the page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadPage('collection_notes.php', document.querySelector('.tab-btn.active'));
        });
    </script>
        
    </body>
</html>

