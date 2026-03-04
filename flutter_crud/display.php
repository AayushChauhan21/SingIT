<?php
// Step 1: API URL (Dynamically built for localhost or live server)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$apiUrl = $protocol . "://" . $host . "/SIngIT/flutter_crud/getSongs.php";

// Step 2: Fetch API response (added @ to suppress warnings if the endpoint is temporarily down)
$response = @file_get_contents($apiUrl);

// Step 3: Decode JSON
$songs = [];
if ($response !== FALSE) {
    $songs = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($songs)) {
        $songs = [];
    }
}

// Step 4: Display in table
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Image</th><th>Title</th><th>Artist</th><th>Duration</th></tr>";

// Only loop if we have valid songs data
if (!empty($songs)) {
    foreach ($songs as $song) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($song['sid'] ?? '') . "</td>";
        echo "<td><img src='" . htmlspecialchars($song['image'] ?? '') . "' height=50/></td>";
        echo "<td>" . htmlspecialchars($song['name'] ?? '') . "</td>";

        // ✅ Artist(s) as comma-separated string
        echo "<td>";
        if (!empty($song['artists']) && is_array($song['artists'])) {
            echo htmlspecialchars(implode(' , ', $song['artists']));
        } else {
            echo "<span style='color: gray;'>No artist</span>";
        }
        echo "</td>";

        echo "<td>" . htmlspecialchars($song['length'] ?? '') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No songs found or error fetching data.</td></tr>";
}

echo "</table>";
?>