<?php
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    echo json_encode(['error' => 'User ID is required.']);
    exit;
}

$user_id = $_GET['user_id'];

$sql = "
    SELECT 
        p.id, 
        p.name,
        
        (SELECT COUNT(*) 
         FROM playlist_songs ps 
         WHERE ps.playlist_id = p.id
        ) AS song_count,
        
        (SELECT 
            SUBSTRING_INDEX(
                GROUP_CONCAT(s.image ORDER BY ps.id DESC SEPARATOR ','), 
                ',', 
                4
            )
         FROM song s
         JOIN playlist_songs ps ON s.sid = ps.song_id
         WHERE ps.playlist_id = p.id
        ) AS images
        
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
        // Split the comma-separated string into an array of images
        if ($row['images']) {
            $row['images'] = explode(',', $row['images']);
        } else {
            $row['images'] = []; // Send an empty array if null
        }
        $playlists[] = $row;
    }
}

// This will return a JSON list of your playlists
echo json_encode($playlists);

$con->close();
?>