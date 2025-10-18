<?php
include("connection.php");

// 🔹 Admin Info
$adminQuery = mysqli_query($con, "SELECT * FROM admin LIMIT 1");
$adminRow = mysqli_fetch_assoc($adminQuery);
$admin = $adminRow ? $adminRow : [];

// 🔹 1. Recent 5 Songs with Artists & Genres
$song5Sql = "SELECT * FROM song ORDER BY sid DESC LIMIT 5";
$song5Result = mysqli_query($con, $song5Sql);

$recent_songs = [];

while ($song = mysqli_fetch_assoc($song5Result)) {
    $song_id = $song['sid'];

    // 🔸 Fetch linked artists
    $artistSql = "SELECT artist.name, artist.arid
                  FROM artist 
                  JOIN artist_song ON artist.arid = artist_song.artist_id 
                  WHERE artist_song.song_id = $song_id";
    $artistResult = mysqli_query($con, $artistSql);

    $artistList = [];
    while ($artist = mysqli_fetch_assoc($artistResult)) {
        $artistList[] = [
            'arid' => $artist['arid'],
            'name' => $artist['name']
        ];
    }
    $song['artists'] = $artistList;

    // 🔸 Fetch linked genres
    $genreSql = "SELECT genre.name, genre.gid 
                 FROM genre 
                 JOIN genre_song ON genre.gid = genre_song.genre_id 
                 WHERE genre_song.song_id = $song_id";
    $genreResult = mysqli_query($con, $genreSql);

    $genreList = [];
    while ($genre = mysqli_fetch_assoc($genreResult)) {
        $genreList[] = [
            'gid' => $genre['gid'],
            'name' => $genre['name']
        ];
    }
    $song['genres'] = $genreList;

    $recent_songs[] = $song;
}

// 🔹 2. Recent 5 Artists
$artistSql = "SELECT * FROM artist ORDER BY arid DESC LIMIT 5";
$artistResult = mysqli_query($con, $artistSql);

$recent_artists = [];
while ($row = mysqli_fetch_assoc($artistResult)) {
    $recent_artists[] = $row;
}

// 🔹 3. Recent 5 Genres
$genreSql = "SELECT * FROM genre ORDER BY gid DESC LIMIT 5";
$genreResult = mysqli_query($con, $genreSql);

$recent_genres = [];
while ($row = mysqli_fetch_assoc($genreResult)) {
    $recent_genres[] = $row;
}

// 🔹 4. Total Counts
$countSongs = mysqli_num_rows(mysqli_query($con, "SELECT * FROM song"));
$countArtists = mysqli_num_rows(mysqli_query($con, "SELECT * FROM artist"));
$countGenres = mysqli_num_rows(mysqli_query($con, "SELECT * FROM genre"));
$countUsers = mysqli_num_rows(mysqli_query($con, "SELECT * FROM user"));

// 🔚 Final JSON Response
header("Content-Type: application/json");
echo json_encode([
    'admin' => $admin,
    'recent_songs' => $recent_songs,
    'recent_artists' => $recent_artists,
    'recent_genres' => $recent_genres,
    'counts' => [
        'songs' => $countSongs,
        'artists' => $countArtists,
        'genres' => $countGenres,
        'users' => $countUsers
    ]
]);
?>