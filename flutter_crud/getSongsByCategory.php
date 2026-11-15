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
  $gid = mysqli_real_escape_string($con, $_GET['gid']);

  // --- QUERY 1: Get the Genre's Details ---
  // અહીં COUNT(DISTINCT gs.song_id) કર્યું છે જેથી ડુપ્લિકેટ ના ગણાય 
  $sql_genre = "SELECT 
                  g.name, 
                  g.image, 
                  COUNT(DISTINCT gs.song_id) AS song_count
                FROM 
                  genre AS g
                LEFT JOIN 
                  genre_song AS gs ON g.gid = gs.genre_id
                WHERE 
                  g.gid = '$gid'
                GROUP BY 
                  g.gid, g.name, g.image";

  $result_genre = $con->query($sql_genre);

  if ($result_genre) {
    if ($result_genre->num_rows > 0) {
      $response['genre_info'] = $result_genre->fetch_assoc();
    }
  } else {
    $response['error'] = "Genre query failed: " . $con->error;
    echo json_encode($response);
    $con->close();
    exit;
  }


  // --- QUERY 2: Get the Songs List ---

  $sql_songs = "SELECT 
                  s.sid,
                  s.name,
                  s.image,
                  s.length,
                  GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS singer_name
                FROM 
                  song AS s
                JOIN
                  artist_song AS a_s ON a_s.song_id = s.sid
                JOIN 
                  artist AS a ON a_s.artist_id = a.arid
                JOIN 
                  genre_song AS gs ON s.sid = gs.song_id
                WHERE 
                  gs.genre_id = '$gid' 
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

    // 🚀 MASTER STROKE: ડેટાબેઝમાં ગમે તે લોચો હોય, લિસ્ટમાં જેટલા સોન્ગ આવ્યા એટલો જ કાઉન્ટ બતાવશે!
    if ($response['genre_info'] !== null) {
      $response['genre_info']['song_count'] = count($songs);
    }

  } else {
    $response['error_songs'] = "Songs query failed: " . $con->error;
  }

} else {
  $response['error'] = "No category ID (gid) provided.";
}

// 5. Close connection and echo the combined JSON
$con->close();
echo json_encode($response);

?>