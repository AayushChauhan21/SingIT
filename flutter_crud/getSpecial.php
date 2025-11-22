<?php
header("Content-Type: application/json");
include 'connection.php';

$sql = "SELECT 
            s.sid, 
            s.name,                  
            s.poster AS image,
            GROUP_CONCAT(a.name SEPARATOR ', ') AS singer_name,
            GROUP_CONCAT(DISTINCT a.arid ORDER BY a.name SEPARATOR ', ') AS artist_ids

        FROM special sl
        JOIN song s ON sl.sid = s.sid
        JOIN artist_song asl ON s.sid = asl.song_id
        JOIN artist a ON asl.artist_id = a.arid
        
        GROUP BY s.sid, s.name, s.poster
        
        ORDER BY sl.id DESC";

$result = $con->query($sql);

$sliderItems = array();

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sliderItems[] = $row;
        }
    }
}

echo json_encode($sliderItems);

$con->close();
?>