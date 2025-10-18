<?php
include("connection.php");

// Set the response content type to JSON
header('Content-Type: application/json');

// Check if a song ID was provided
if (!isset($_GET["sid"])) {
    echo json_encode(["error" => "No id provided"]);
    exit;
}

// NOTE: Directly using $_GET["sid"] (not safe for production).
// Use prepared statements (mysqli_prepare/bind_param) in a real application.
$sid = $_GET["sid"];

// SQL query: get song details, singers, genres, AND LANGUAGES
$qry = "
    SELECT 
        s.sid, 
        s.name, 
        s.image,
        s.poster,
        s.length, 
        s.instrumental, 
        s.vocal, 
        s.lyrics,
        GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS singer_name,
        GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') AS genre_names,
        GROUP_CONCAT(DISTINCT l.name SEPARATOR ', ') AS language_names -- Added language names
    FROM 
        song AS s
    LEFT JOIN 
        artist_song AS asa ON s.sid = asa.song_id
    LEFT JOIN 
        artist AS a ON asa.artist_id = a.arid
    LEFT JOIN 
        genre_song AS gsa ON s.sid = gsa.song_id
    LEFT JOIN 
        genre AS g ON gsa.genre_id = g.gid
    
    -- LEFT JOINS for Language (UPDATED based on your language_song table)
    LEFT JOIN 
        language_song AS lsa ON s.sid = lsa.song_id      -- Join using song_id
    LEFT JOIN 
        language AS l ON lsa.language_id = l.lid         -- Join using language_id = lid
        
    WHERE 
        s.sid = '$sid'
    GROUP BY 
        s.sid
";

$res = mysqli_query($con, $qry);

if ($res && mysqli_num_rows($res) > 0) {
    $data = mysqli_fetch_assoc($res);

    // Map singers
    $data['singer'] = $data['singer_name'] ?? 'Unknown Singer';
    unset($data['singer_name']);

    // Map genres
    $data['genres'] = $data['genre_names'] ?? 'Unknown Genre';
    unset($data['genre_names']);

    // Map languages (NEW MAPPING)
    $data['languages'] = $data['language_names'] ?? 'Unknown Language';
    unset($data['language_names']);

    echo json_encode($data);
} else {
    echo json_encode(["error" => "Song not found"]);
}
?>