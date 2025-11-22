<?php
include("connection.php");
header('Content-Type: application/json');

$response = [
    'songs' => [],
    'artists' => []
];

if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
    echo json_encode($response);
    exit;
}

// Get and escape the search query
$query = trim($_GET['query']);
$escaped_query = mysqli_real_escape_string($con, $query);
$search_term = "%" . $escaped_query . "%";

// --- 1. Search for Songs (NOW INCLUDES SINGER NAME) ---
$sql_song = "
    SELECT 
        s.sid, 
        s.name, 
        s.image,
        -- Get a comma-separated list of artist names for this song
        GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS singer_name 
    FROM 
        song AS s
    LEFT JOIN 
        artist_song AS asa ON s.sid = asa.song_id
    LEFT JOIN 
        artist AS a ON asa.artist_id = a.arid
    WHERE 
        s.name LIKE '$search_term'
    GROUP BY 
        s.sid
";

$res_song = mysqli_query($con, $sql_song);

if ($res_song) {
    while($row = mysqli_fetch_assoc($res_song)) {
        $row['type'] = 'song'; 
        // Handle songs that might not have a linked artist
        if (is_null($row['singer_name'])) {
            $row['singer_name'] = 'Unknown Artist';
        }
        $response['songs'][] = $row;
    }
}

// --- 2. Search for Artists (NOW INCLUDES SONG COUNT) ---
$sql_artist = "
    SELECT 
        a.arid, 
        a.name, 
        a.photo,
        -- Count the number of unique songs linked to this artist
        COUNT(DISTINCT asa.song_id) AS song_count
    FROM 
        artist AS a
    LEFT JOIN 
        artist_song AS asa ON a.arid = asa.artist_id
    WHERE 
        a.name LIKE '$search_term'
    GROUP BY 
        a.arid
";

$res_artist = mysqli_query($con, $sql_artist);

if ($res_artist) {
    while($row = mysqli_fetch_assoc($res_artist)) {
        $row['type'] = 'artist';
        $response['artists'][] = $row;
    }
}

// --- 3. Return the combined JSON ---
echo json_encode($response);
?>