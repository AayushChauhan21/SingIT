<?php
header('Content-Type: application/json');
include 'connection.php';

// 1. Get the user_id from the URL
$user_id = $_GET['user_id'];

// 2. Build the SQL query
$sql = "
    SELECT 
    s.sid, 
    s.name, 
    s.image, 
    GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS artist_names
FROM 
    history h
JOIN 
    song s ON h.song_id = s.sid
LEFT JOIN 
    artist_song asa ON s.sid = asa.song_id -- Join to the linking table
LEFT JOIN 
    artist a ON asa.artist_id = a.arid   -- Join to the artist table
WHERE 
    h.user_id = '$user_id'
-- We must group by the song to combine all artists for that one song
GROUP BY 
    s.sid, s.name, s.image
-- Now we order by the most recent history item
ORDER BY 
    h.id DESC";

// 3. Run the query
$result = mysqli_query($con, $sql);

// 4. Fetch all songs into an array
$songs = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $songs[] = $row;
    }
}

// 5. Echo the array as JSON
echo json_encode($songs);

// 6. Close the connection
$con->close();

?>