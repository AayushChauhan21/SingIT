<?php
include("connection.php");
header('Content-Type: application/json');

// --- Check for artist ID ---
if (!isset($_GET['arid']) || empty(trim($_GET['arid']))) {
    echo json_encode(['error' => 'Artist ID (arid) not provided']);
    exit;
}

$artist_id = mysqli_real_escape_string($con, trim($_GET['arid']));

$response = [
    'details' => null,
    'songs' => []
];

// --- 1. Get Artist Details (including description and song count) ---
$sql_details = "
    SELECT 
        a.arid, 
        a.name, 
        a.photo, 
        a.description,
        a.image, 
        COUNT(DISTINCT asa.song_id) AS song_count
    FROM 
        artist AS a
    LEFT JOIN 
        artist_song AS asa ON a.arid = asa.artist_id
    WHERE 
        a.arid = '$artist_id'
    GROUP BY 
        a.arid 
    LIMIT 1 
";

$res_details = mysqli_query($con, $sql_details);

if ($res_details && mysqli_num_rows($res_details) > 0) {
    // $response['details'] = mysqli_fetch_assoc($res_details);

    // if (!isset($artist_data['description']) || trim($artist_data['description']) === '') {
    //     $artist_data['description'] = 'No description...';
    // }

    $artist_data = mysqli_fetch_assoc($res_details);

    // Check if description is empty or null
    if (!isset($artist_data['description']) || trim($artist_data['description']) === '') {
        $artist_data['description'] = 'No description...';
    }

    $response['details'] = $artist_data;

} else {
    // Artist not found, return error
    echo json_encode(['error' => 'Artist not found']);
    exit;
}

// --- 2. Get Songs by this Artist ---
$sql_songs = "
    SELECT 
        s.sid, 
        s.name, 
        s.image,
        s.length,
        -- This subquery gets all artists for THIS song
        (SELECT GROUP_CONCAT(a.name SEPARATOR ', ') 
         FROM artist a 
         JOIN artist_song asa_inner ON a.arid = asa_inner.artist_id 
         WHERE asa_inner.song_id = s.sid
        ) AS artist_names
    FROM 
        song AS s
    JOIN 
        artist_song AS asa ON s.sid = asa.song_id
    WHERE 
        asa.artist_id = '$artist_id' -- Get all songs linked to the main artist
    GROUP BY -- Add GROUP BY to ensure one row per song
        s.sid, s.name, s.image
    ORDER BY 
        s.name ASC 
";

$res_songs = mysqli_query($con, $sql_songs);

if ($res_songs) {
    while ($row = mysqli_fetch_assoc($res_songs)) {
        $response['songs'][] = $row;
    }
}

// --- 3. Return JSON ---
echo json_encode($response);
?>