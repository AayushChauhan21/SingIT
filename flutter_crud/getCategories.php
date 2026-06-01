<?php

header("Content-Type: application/json");
include 'connection.php';

$response = [];

$sql = "SELECT 
            g.gid AS id, 
            g.name, 
            g.image, 
            COUNT(gs.song_id) AS song_count
        FROM 
            genre AS g
        LEFT JOIN 
            genre_song AS gs ON g.gid = gs.genre_id
        GROUP BY 
            g.gid, g.name, g.image
        ORDER BY 
            g.gid ASC";

$result = $con->query($sql);

if ($result) {
    $categories = [];
    
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    $response['categories'] = $categories;
    
} else {
    $response['error'] = "Query failed: " . $con->error;
}

$con->close();

echo json_encode($response);

?>