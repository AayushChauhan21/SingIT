<?php
include("connection.php");

$sql = "SELECT * FROM song ORDER BY sid DESC";
$result = mysqli_query($con, $sql);

header("Content-Type: application/json");
$songs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $songs[] = $row;
}

echo json_encode($songs);
?>