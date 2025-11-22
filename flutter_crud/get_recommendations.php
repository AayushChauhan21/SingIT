<?php
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    echo json_encode([]);
    exit;
}

// 1. Sanitize User ID
$user_id = $_GET['user_id'];

// 2. Find all genres from the user's favorite songs
$fav_data = [];
$fav_sql = "
    SELECT 
        gs.genre_id, 
        f.song_id 
    FROM 
        favourite f
    JOIN 
        genre_song gs ON f.song_id = gs.song_id
    WHERE 
        f.user_id = '$user_id'
";

$fav_result = $con->query($fav_sql);

if ($fav_result->num_rows == 0) {
    // User has no favorites, so we can't recommend anything.
    echo json_encode([]);
    $con->close();
    exit;
}

$genre_ids = [];
$fav_song_ids = [];

while ($row = $fav_result->fetch_assoc()) {
    $genre_ids[] = $row['genre_id'];
    $fav_song_ids[] = $row['song_id'];
}

// Make sure IDs are unique
$genre_ids_unique = array_unique($genre_ids);
$fav_song_ids_unique = array_unique($fav_song_ids);

// Create safe, comma-separated strings for the 'IN' clause
$genre_list = implode(',', $genre_ids_unique);
$fav_song_list = implode(',', $fav_song_ids_unique);

// 3. Find songs that match those genres,
//    but are NOT in the user's favorites.
$rec_sql = "
    SELECT DISTINCT
        s.sid,
        s.name,
        s.image
    FROM 
        song s
    JOIN 
        genre_song gs ON s.sid = gs.song_id
    WHERE 
        gs.genre_id IN ($genre_list) 
    AND 
        s.sid NOT IN ($fav_song_list)
    ORDER BY 
        RAND() -- Get random songs
    LIMIT 10 -- Limit to 10 recommendations
";

$rec_result = $con->query($rec_sql);
$recommendations = [];

if ($rec_result) {
    while ($rec_row = $rec_result->fetch_assoc()) {
        $recommendations[] = $rec_row;
    }
}

// 4. Return the recommendations
echo json_encode($recommendations);
$con->close();
?>