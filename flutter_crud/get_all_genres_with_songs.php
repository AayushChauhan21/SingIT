<?php
header('Content-Type: application/json');
include 'connection.php';

// --- 1. Get all Genres ---
$genres_map = [];
$genre_sql = "SELECT gid, name FROM genre ORDER BY gid";
$genre_result = $con->query($genre_sql);

if (!$genre_result) {
    echo json_encode(['error' => 'Failed to fetch genres: ' . $con->error]);
    exit;
}

while ($row = $genre_result->fetch_assoc()) {
    $row['songs'] = [];
    $genres_map[$row['gid']] = $row;
}

// --- 2. Get all Songs and link them to Genres ---
$song_sql = "
    SELECT 
        s.sid, 
        s.name, 
        s.image,
        gs.genre_id 
    FROM 
        song s
    JOIN 
        genre_song gs ON s.sid = gs.song_id
";

$song_result = $con->query($song_sql);

if (!$song_result) {
    echo json_encode(['error' => 'Failed to fetch songs: ' . $con->error]);
    exit;
}

// --- 3. Combine the Data ---
while ($song_row = $song_result->fetch_assoc()) {
    $genre_id = $song_row['genre_id'];

    // Check if this song's genre_id exists in our genre map
    if (isset($genres_map[$genre_id])) {
        
        // Create a clean song array (without the extra genre_id field)
        $song_data = [
            'sid' => $song_row['sid'],
            'name' => $song_row['name'],
            'image' => $song_row['image']
        ];

        // Add this song data into the 'songs' array of the correct genre
        $genres_map[$genre_id]['songs'][] = $song_data;
    }
}

// --- 4. Final Output ---
$final_output = array_values($genres_map);

echo json_encode($final_output);

$con->close();
?>