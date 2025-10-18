<?php
include("connection.php");

// 🔹 Step 1: Fetch all songs
$songSql = "SELECT * FROM song ORDER BY sid DESC";
$songResult = mysqli_query($con, $songSql);

$songs = [];
while ($song = mysqli_fetch_assoc($songResult)) {
    $songs[] = $song;
}

// 🔚 Step 2: Return JSON
header("Content-Type: application/json");
echo json_encode($songs);
?>