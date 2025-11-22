<?php
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_GET['user_id']) || empty($_GET['user_id']) || (empty($_GET['song_id']))) {
    echo json_encode(['error' => 'User ID and Song ID is required.']);
    exit;
}

// Get both user_id and song_id
$user_id=$_GET['user_id'];
$song_id=$_GET['song_id'];

$sql = "
    SELECT 
        p.id, 
        p.name,
        
        (SELECT COUNT(*) 
         FROM playlist_songs ps 
         WHERE ps.playlist_id = p.id
        ) AS song_count,
        
        (SELECT s.image 
         FROM song s
         JOIN playlist_songs ps ON s.sid = ps.song_id
         WHERE ps.playlist_id = p.id
         ORDER BY ps.id DESC
         LIMIT 1
        ) AS image,

        EXISTS (
            SELECT 1 
            FROM playlist_songs ps 
            WHERE ps.playlist_id = p.id AND ps.song_id = '$song_id'
        ) AS contains_song -- This will return 1 (true) or 0 (false)
        
    FROM 
        playlists p
    WHERE 
        p.user_id = '$user_id'
    ORDER BY 
        p.name ASC
";

$result = $con->query($sql);
$playlists = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['contains_song'] = (bool)$row['contains_song'];
        $playlists[] = $row;
    }
}

echo json_encode($playlists);

$con->close();
?>