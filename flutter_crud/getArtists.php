<?php
include("connection.php");

$sql = "SELECT * FROM artist ORDER BY arid desc";
$result = mysqli_query($con, $sql);

$artists = [];
while ($row = mysqli_fetch_assoc($result)) {
    $artists[] = $row;
}

header("Content-Type: application/json");
echo json_encode($artists);
?>