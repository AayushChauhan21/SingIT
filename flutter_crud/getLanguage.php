<?php
include("connection.php");

// 🔹 Step 1: Fetch all language
$sql = "SELECT * FROM language ORDER BY lid DESC";
$result = mysqli_query($con, $sql);

$languages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $languages[] = $row;
}

// 🔚 Step 2: Return JSON
header("Content-Type: application/json");
echo json_encode($languages);

?>