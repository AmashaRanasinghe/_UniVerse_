<?php
$server = "localhost";
$userName = "root";
$password = "";
$dbname = "universe";

$conn = mysqli_connect($server, $userName, $password, $dbname);
global $conn;
if (!$conn) {
    echo "error in database connection";
} else {
}
