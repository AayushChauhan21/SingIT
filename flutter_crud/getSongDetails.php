<?php
header('Content-Type: application/json');
include 'connection.php'; // Make sure this path is correct

if (!isset($_GET['sid']) || empty($_GET['sid'])) {
    echo json_encode(['error' => 'Song ID (sid) is required.']);
    exit;
}

// Use prepared statements in a real app!
$sid = $con->real_escape_string($_GET['sid']);

// --- 1. Get Main Song Details (and Languages using GROUP_CONCAT) ---
$songResult = $con->query("
    SELECT 
        s.sid, 
        s.name, 
        s.image,
        s.poster,
        s.length, 
        s.instrumental, 
        s.vocal, 
        s.lyrics,
        s.album,
        GROUP_CONCAT(DISTINCT l.name SEPARATOR ', ') AS language_names
    FROM 
        song AS s
    LEFT JOIN 
        language_song AS lsa ON s.sid = lsa.song_id
    LEFT JOIN 
        language AS l ON lsa.language_id = l.lid
    WHERE 
        s.sid = '$sid'
    GROUP BY 
        s.sid
");

if ($songResult->num_rows == 0) {
    echo json_encode(['error' => 'Song not found.']);
    exit;
}

$songDetails = $songResult->fetch_assoc();

// --- 2. Get Singers (as an array of objects) ---
$singers = [];
$singersQuery = "
    SELECT a.arid AS id, a.name 
    FROM artist AS a
    LEFT JOIN artist_song AS asa ON a.arid = asa.artist_id
    WHERE asa.song_id = '$sid'
    ORDER BY a.name
";
$singersResult = $con->query($singersQuery);
while ($row = $singersResult->fetch_assoc()) {
    $singers[] = $row; // Adds each singer object to the array
}

// --- 3. Get Genres (as an array of objects) ---
$genres = [];
$genresQuery = "
    SELECT g.gid AS id, g.name
    FROM genre AS g
    LEFT JOIN genre_song AS gsa ON g.gid = gsa.genre_id
    WHERE gsa.song_id = '$sid'
    ORDER BY g.name
";
$genresResult = $con->query($genresQuery);
while ($row = $genresResult->fetch_assoc()) {
    $genres[] = $row; // Adds each genre object to the array
}

// --- 4. Assemble the Final JSON ---
$output = [
    'sid' => $songDetails['sid'],
    'name' => $songDetails['name'],
    'image' => $songDetails['image'],
    'poster' => $songDetails['poster'],
    'length' => $songDetails['length'],
    'instrumental' => $songDetails['instrumental'],
    'vocal' => $songDetails['vocal'],
    'lyrics' => $songDetails['lyrics'],
    'album' => $songDetails['album'],
    'languages' => $songDetails['language_names'], // This is the string
    'singers' => $singers, // This is the array
    'genres' => $genres     // This is the array
];

echo json_encode($output);

$con->close();
?>