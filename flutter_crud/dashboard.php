<?php
include("connection.php");

// 🔹 Admin Info
$adminQuery = mysqli_query($con, "SELECT * FROM admin LIMIT 1");
$adminRow = mysqli_fetch_assoc($adminQuery);
$admin = $adminRow ? $adminRow : [];

// 🔹 1. Recent 5 Songs with Artists & Genres (OPTIMIZED SQL)
$song5Sql = "
    SELECT 
        s.*, 
        GROUP_CONCAT(DISTINCT a.name ORDER BY a.name SEPARATOR ', ') AS artist_names,
        GROUP_CONCAT(DISTINCT a.arid ORDER BY a.name SEPARATOR ', ') AS artist_ids,
        GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') AS genre_names,
        GROUP_CONCAT(DISTINCT g.gid ORDER BY g.name SEPARATOR ', ') AS genre_ids
    FROM 
        song AS s
    LEFT JOIN 
        artist_song AS aso ON s.sid = aso.song_id 
    LEFT JOIN 
        artist AS a ON aso.artist_id = a.arid 
    LEFT JOIN 
        genre_song AS gso ON s.sid = gso.song_id
    LEFT JOIN 
        genre AS g ON gso.genre_id = g.gid
    GROUP BY
        s.sid /* Group by song ID to consolidate results */
    ORDER BY 
        s.sid DESC 
    LIMIT 5
";
$song5Result = mysqli_query($con, $song5Sql);

$recent_songs = [];

while ($row = mysqli_fetch_assoc($song5Result)) {
    $recent_songs[] = $row;
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

// 🔹 4. Recent 5 Language
$languageSql = "SELECT * FROM language ORDER BY lid DESC LIMIT 5";
$languageResult = mysqli_query($con, $languageSql);

$recent_languages = [];
while ($row = mysqli_fetch_assoc($languageResult)) {
    $recent_languages[] = $row;
}

// 🔹 5. Recent 5 Slider (Kept your original optimized logic)
$sliderSql = "
    SELECT 
        s.id, s.sid, sg.poster AS song_poster, sg.name AS song_name, 
        GROUP_CONCAT(DISTINCT a.name ORDER BY a.name SEPARATOR ', ') AS artist_names,
        GROUP_CONCAT(DISTINCT a.arid ORDER BY a.name SEPARATOR ', ') AS artist_ids
    FROM 
        slider AS s
    LEFT JOIN 
        song AS sg ON s.sid = sg.sid 
    LEFT JOIN 
        artist_song AS aso ON sg.sid = aso.song_id 
    LEFT JOIN 
        artist AS a ON aso.artist_id = a.arid 
    GROUP BY
        s.id 
    ORDER BY 
        s.id DESC 
    LIMIT 5
";
$sliderResult = mysqli_query($con, $sliderSql);

$recent_sliders = [];
while ($row = mysqli_fetch_assoc($sliderResult)) {
    // Handle case where song/artist link is missing
    if (is_null($row['song_name'])) {
        $row['song_name'] = 'N/A';
        $row['artist_names'] = 'N/A';
    }
    if (is_null($row['artist_names'])) {
        $row['artist_names'] = 'Unknown Artist';
    }

    $recent_sliders[] = $row;
}


// 🔹 6. ADDED: Recent 5 Special Songs
$specialSql = "
    SELECT 
        sp.id, sp.sid, sg.poster AS song_poster, sg.name AS song_name, 
        GROUP_CONCAT(DISTINCT a.name ORDER BY a.name SEPARATOR ', ') AS artist_names
    FROM 
        special AS sp
    LEFT JOIN 
        song AS sg ON sp.sid = sg.sid 
    LEFT JOIN 
        artist_song AS aso ON sg.sid = aso.song_id 
    LEFT JOIN 
        artist AS a ON aso.artist_id = a.arid 
    GROUP BY sp.id 
    ORDER BY sp.id DESC 
    LIMIT 5
";
$specialResult = mysqli_query($con, $specialSql);
$recent_special = [];
while ($row = mysqli_fetch_assoc($specialResult)) {
    if (is_null($row['song_name'])) {
        $row['song_name'] = 'N/A';
    }
    $recent_special[] = $row;
}


// 🔹 7. Total Counts
$countSongs = mysqli_num_rows(mysqli_query($con, "SELECT * FROM song"));
$countArtists = mysqli_num_rows(mysqli_query($con, "SELECT * FROM artist"));
$countGenres = mysqli_num_rows(mysqli_query($con, "SELECT * FROM genre"));
$countUsers = mysqli_num_rows(mysqli_query($con, "SELECT * FROM user"));
$countSlider = mysqli_num_rows(mysqli_query($con, "SELECT * FROM slider"));
$countLang = mysqli_num_rows(mysqli_query($con, "SELECT * FROM language"));


// 🔚 Final JSON Response
header("Content-Type: application/json");
echo json_encode([
    'admin' => $admin,
    'recent_songs' => $recent_songs,
    'recent_artists' => $recent_artists,
    'recent_genres' => $recent_genres,
    'recent_languages' => $recent_languages,
    'recent_sliders' => $recent_sliders,
    'recent_special' => $recent_special, // <--- NOW INCLUDED
    'counts' => [
        'songs' => $countSongs,
        'artists' => $countArtists,
        'genres' => $countGenres,
        'users' => $countUsers,
        'sliders' => $countSlider,
        'languages' => $countLang
    ]
]);

// Note: In a production environment, you should close the connection here.
// mysqli_close($con); 
?>