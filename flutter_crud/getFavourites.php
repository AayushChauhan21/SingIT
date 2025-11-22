<?php

header("Content-Type: application/json");
include 'connection.php'; 

$response = [
    'songs_count'=>0,
    'songs' => []
];

// 1. Get the User ID from the URL
if (isset($_GET['user_id'])) {
    
    $user_id = $_GET['user_id'];

    // --- QUERY 1: Get the "Favourites" song_count Info ---
    
    $sql_header = "SELECT COUNT(*) AS song_count FROM favourite WHERE user_id = $user_id";
    $result_header = $con->query($sql_header);

    $song_count = 0; // Default
    if ($result_header && $result_header->num_rows > 0) {
        $row = $result_header->fetch_assoc();
        $song_count = $row['song_count'];
    }

    $response['songs_count'] = (int)$song_count;

    // --- QUERY 2: Get the List of Favourite Songs ---
    
    $sql_songs = "SELECT 
                    s.sid,
                    s.name,
                    s.image, 
                    GROUP_CONCAT(a.name SEPARATOR ', ') AS singer_name
                FROM 
                    favourite AS f
                JOIN
                    song AS s ON f.song_id = s.sid
                LEFT JOIN
                    artist_song AS a_s ON a_s.song_id = s.sid
                LEFT JOIN 
                    artist AS a ON a_s.artist_id = a.arid
                WHERE 
                    f.user_id = $user_id 
                GROUP BY 
                    s.sid, s.name, s.image
                ORDER BY
                    f.id DESC"; // Order by newest favourites first

    $result_songs = $con->query($sql_songs);

    if ($result_songs) {
        $songs = [];
        while($row = $result_songs->fetch_assoc()) {
            // Cast sid to string to be safe for Dart
            $row['sid'] = (string)$row['sid']; 
            $songs[] = $row;
        }
        // Add the songs array to the 'songs' key
        $response['songs'] = $songs;
    } else {
        // Add a specific error if the songs query fails
        $response['error_songs'] = "Songs query failed: " . $con->error;
    }

} else {
    $response['error'] = "No user ID (user_id) provided.";
}

// 5. Close connection and echo the combined JSON
$con->close();
echo json_encode($response);

?>