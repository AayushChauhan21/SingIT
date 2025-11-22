<?php
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_GET['playlist_id']) || empty($_GET['playlist_id'])) {
    echo json_encode(['error' => 'Playlist ID is required.']);
    exit;
}

// 1. Get and sanitize the playlist_id from the URL
$playlist_id = $_GET['playlist_id'];

// 2. Write the SQL query
$sql = "
    SELECT 
        s.sid, 
        s.name, 
        s.image,
        
        (SELECT GROUP_CONCAT(a.name SEPARATOR ', ')
         FROM artist a
         JOIN artist_song asa ON a.arid = asa.artist_id
         WHERE asa.song_id = s.sid
        ) AS singer
    FROM 
        song s
    JOIN 
        playlist_songs ps ON s.sid = ps.song_id
    WHERE 
        ps.playlist_id = '$playlist_id'
    ORDER BY 
        ps.id DESC -- Shows most recently added songs first
";

// 3. Execute the query
$result = $con->query($sql);
$songs = [];

if ($result) {
    // 4. Fetch all the songs into an array
    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }
} else {
    // Handle query error, though not strictly required by your simple format
    echo json_encode(['error' => 'Failed to fetch songs: ' . $con->error]);
    $con->close();
    exit;
}

// 5. Return the list of songs as JSON
echo json_encode($songs);

// 6. Close the connection
$con->close();
?>