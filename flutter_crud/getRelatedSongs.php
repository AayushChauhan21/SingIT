<?php
// config.php should contain your database connection details
include 'connection.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Check if sid is provided
if (!isset($_GET['sid']) || empty($_GET['sid'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Song ID (sid) is required."]);
    exit;
}

$current_sid = $con->real_escape_string($_GET['sid']);

// 1. Get the genre IDs for the current song
$genre_query = "
    SELECT genre_id
    FROM genre_song
    WHERE song_id = '$current_sid'
";

$genre_result = $con->query($genre_query);

if ($genre_result === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Database query failed: " . $con->error]);
    exit;
}

$genre_ids = [];
while ($row = $genre_result->fetch_assoc()) {
    $genre_ids[] = (int)$row['genre_id'];
}

// Check if the current song has any genres
if (empty($genre_ids)) {
    // If no genres, return an empty list of related songs
    echo json_encode([]);
    exit;
}

// Convert genre IDs to a comma-separated string for the SQL IN clause
$genre_list = implode(',', $genre_ids);

// 2. Find other songs that share any of these genres (excluding the current song)
// We limit to 10 for demonstration and order randomly to provide variety, then group
// by song_id to ensure each song appears only once, even if it has multiple matching genres.
$related_songs_query = "
    SELECT DISTINCT s.sid, s.name, s.image, GROUP_CONCAT(DISTINCT sg.genre_id) AS matched_genres
    FROM song s
    JOIN genre_song sg ON s.sid = sg.song_id
    WHERE sg.genre_id IN ($genre_list)
    AND s.sid != '$current_sid'
    GROUP BY s.sid
    ORDER BY s.sid desc
    LIMIT 10
";

$related_result = $con->query($related_songs_query);

if ($related_result === false) {
    http_response_code(500);
    echo json_encode(["error" => "Related songs query failed: " . $con->error]);
    exit;
}

$related_songs = [];
while ($row = $related_result->fetch_assoc()) {
    // Return relevant fields. Use 'image' as requested for the related list.
    $related_songs[] = [
        'sid' => $row['sid'],
        'name' => $row['name'],
        'image' => $row['image'] ?? 'https://placehold.co/100'
    ];
}

// 3. Return the result
echo json_encode($related_songs);

// Close the database connection
$con->close();
?>