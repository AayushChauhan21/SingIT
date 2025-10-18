<?php
include("connection.php");

// 🔹 Step 1: Fetch all artist
$sql = "SELECT * FROM artist ORDER BY arid DESC";
$result = mysqli_query($con, $sql);

$artists = [];
while ($row = mysqli_fetch_assoc($result)) {
    $artists[] = $row;
}

// 🔚 Step 2: Return JSON
header("Content-Type: application/json");
echo json_encode($artists);

?>