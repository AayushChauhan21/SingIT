<?php
include("connection.php");

$arid = $_GET['arid'];

$artistSql = "SELECT * FROM artist WHERE arid = $arid";
$artistResult = mysqli_query($con, $artistSql);
$artist = mysqli_fetch_assoc($artistResult);

$songSql = "SELECT song.sid, song.name, song.album, song.length, song.image
            FROM song, artist_song
            WHERE song.sid = artist_song.song_id
              AND artist_song.artist_id = $arid
            ORDER BY song.sid DESC";

$songResult = mysqli_query($con, $songSql);

$songs = [];
while ($row = mysqli_fetch_assoc($songResult)) {
    $songs[] = $row;
}

header("Content-Type: application/json");

if ($artist) {
    $artist['songs'] = $songs;
    echo json_encode($artist);
} else {
    echo json_encode(['error' => 'Artist not found']);
}
?>