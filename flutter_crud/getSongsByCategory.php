<?php

header("Content-Type: application/json");
include 'connection.php';

$response = [
  'genre_info' => null,
  'songs' => []
];

// 1. Get the category ID from the URL
if (isset($_GET['gid'])) {

  // --- IMPORTANT: Sanitize the ID to prevent SQL injection ---
  $gid = $_GET['gid'];

  // --- QUERY 1: Get the Genre's Details (from your reference code) ---

  $sql_genre = "SELECT 
                    g.name, 
                    g.image, 
                    COUNT(gs.song_id) AS song_count
                  FROM 
                    genre AS g
                  LEFT JOIN 
                    genre_song AS gs ON g.gid = gs.genre_id
                  WHERE 
                    g.gid = $gid
                  GROUP BY 
                    g.gid, g.name, g.image"; // Group by the specific genre

  $result_genre = $con->query($sql_genre);

  if ($result_genre) {
    if ($result_genre->num_rows > 0) {
      // Fetch the details and add them to the 'genre_info' key
      $response['genre_info'] = $result_genre->fetch_assoc();
    }
    // If no rows, 'genre_info' just stays null, which is fine.
  } else {
    $response['error'] = "Genre query failed: " . $con->error;
    // Echo early if this critical part fails
    echo json_encode($response);
    $con->close();
    exit; // Stop the script
  }


  // --- QUERY 2: Get the Songs List (from your original script) ---

  $sql_songs = "SELECT 
                    s.sid,
                    s.name,
                    s.image,
                    s.length,
                    GROUP_CONCAT(a.name SEPARATOR ', ') AS singer_name
                  FROM 
                    song AS s
                  JOIN
                    artist_song AS a_s ON a_s.song_id = s.sid
                  JOIN 
                    artist AS a ON a_s.artist_id = a.arid
                  JOIN 
                    genre_song AS gs ON s.sid = gs.song_id
                  WHERE 
                    gs.genre_id = $gid 
                  GROUP BY 
                    s.sid
                  ORDER BY
                    s.name ASC";

  $result_songs = $con->query($sql_songs);

  if ($result_songs) {
    $songs = [];
    while ($row = $result_songs->fetch_assoc()) {
      $songs[] = $row;
    }
    // Add the songs array to the 'songs' key
    $response['songs'] = $songs;
  } else {
    // Add a specific error if the songs query fails
    $response['error_songs'] = "Songs query failed: " . $con->error;
  }

} else {
  $response['error'] = "No category ID (gid) provided.";
}

// 5. Close connection and echo the combined JSON
$con->close();
echo json_encode($response);

?>