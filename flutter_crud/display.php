<?php
// Step 1: API URL
$apiUrl = "http://localhost/SIngIT/flutter_crud/getSongs.php";

// Step 2: Fetch API response
$response = file_get_contents($apiUrl);

// Step 3: Decode JSON
$songs = json_decode($response, true);

// Step 4: Display in table
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Title</th><th>Title</th><th>Artist</th><th>Duration</th></tr>";

foreach ($songs as $song) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($song['sid']) . "</td>";
    echo "<td><img src='" . htmlspecialchars($song['image']) . "' height=50/></td>";
    echo "<td>" . htmlspecialchars($song['name']) . "</td>";

    // ✅ Artist(s) as comma-separated string
    echo "<td>";
    if (!empty($song['artists'])) {
        echo htmlspecialchars(implode(' , ', $song['artists']));
    } else {
        echo "<span style='color: gray;'>No artist</span>";
    }
    echo "</td>";

    echo "<td>" . htmlspecialchars($song['length']) . "</td>";
    echo "</tr>";
}

echo "</table>";
?>