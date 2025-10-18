<?php
include("connection.php");

// 🔹 Step 1: Fetch all genre
$sql = "SELECT * FROM genre ORDER BY gid DESC";
$result = mysqli_query($con, $sql);

$genres = [];
while ($row = mysqli_fetch_assoc($result)) {
    $genres[] = $row;
}

// 🔚 Step 2: Return JSON
header("Content-Type: application/json");
echo json_encode($genres);

?>