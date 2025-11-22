<?php
include("connection.php");

$sql = "SELECT * FROM song"; 
$result = mysqli_query($con, $sql);

header("Content-Type: application/json");
$songs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $songs[] = $row;
}

echo json_encode($songs);
?>