<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../connection.php');
session_start();
// fetching users
$s_sql = "SELECT firstname, created_at FROM users WHERE role = 'Student'";
$s_result = $conn->query($s_sql);

$users = [];

while ($c_row = $s_result->fetch_assoc()) {
    $users[] = $c_row;
}

$userData = json_encode($users);

$labels = array_map(function ($user) {
    return date('Y-m-d', strtotime($user['created_at']));
}, $users);

$values = array_count_values($labels);
$dates = array_keys($values);
$counts = array_values($values);

$dataForChart = [];
foreach ($dates as $date) {
    $dataForChart[] = isset($values[$date]) ? $values[$date] : 0;
}

// fetching content
$c_sql = "SELECT created_at FROM content"; // Adjust the query as needed
$c_result = $conn->query($c_sql);

$contentData = [];
while ($c_row = $c_result->fetch_assoc()) {
    $contentData[] = $c_row;
}

$contentLabels = array_map(function ($content) {
    return date('Y-m-d', strtotime($content['created_at']));
}, $contentData);

$contentValues = array_count_values($contentLabels);
$contentDates = array_keys($contentValues);
$contentCounts = array_values($contentValues);

$dataForContentChart = [];
foreach ($contentDates as $date) {
    $dataForContentChart[] = isset($contentValues[$date]) ? $contentValues[$date] : 0;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
    <style>
        <?php include("../styles/analytics.css") ?>
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php require('../admin/sidebar.php') ?>
    <div class="dash">

        <div class="dash-header">
            <div class="dash-h-left">
                <h1>Analytics</h1>
                <?php
                date_default_timezone_set('Asia/Colombo');
                echo date("l, F j, Y g:i A", time())
                ?>
            </div>

            <div class="dash-h-right">
                <p>Hello, Primary Admin!</p>
            </div>
        </div>

        <!-- user stats -->
        <div class="user-stats">
            <div class="u-s-user">
                <h1>Students</h1>
                <p>Total Students: <?php echo count($users); ?></p>
                <canvas id="userChart" width="100" height="100"></canvas>
            </div>
            <div class="u-s-user">
                <h1>Content</h1>
                <p>Total Content: <?php echo count($contentCounts); ?></p>
                <canvas id="contentChart" width="100" height="100"></canvas>
            </div>
        </div>

    </div>
    <script>
        // users
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('userChart');
            if (canvas) {
                const context = canvas.getContext('2d');
                const userChart = new Chart(context, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($dates); ?>,
                        datasets: [{
                            label: 'Student Count',
                            data: <?php echo json_encode($dataForChart); ?>,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgb(2, 58, 29)',
                            borderWidth: 1.5,
                            fill: false
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: false,
                                stepSize: 1,
                            }
                        }
                    }
                });
            } else {
                console.error('Canvas element not found.');
            }

            // content
            const contentCanvas = document.getElementById('contentChart');
            if (contentCanvas) {
                const contentContext = contentCanvas.getContext('2d');
                const contentChart = new Chart(contentContext, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($contentDates); ?>,
                        datasets: [{
                            label: 'Content Count',
                            data: <?php echo json_encode($dataForContentChart); ?>,
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgb(153, 102, 255)',
                            borderWidth: 1.5,
                            fill: false
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: false,
                                stepSize: 1,
                            }
                        },

                        y: {
                            beginAtZero: false,
                            stepSize: 1,
                        },
                    }
                });
            } else {
                console.error('Content canvas element not found.');
            }
        });
    </script>
</body>