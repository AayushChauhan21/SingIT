<?php
header('Content-Type: application/json');
include 'connection.php';

$playlist_id = $_GET['playlist_id'];
$song_id = $_GET['song_id'];

// Validation: Check if EITHER variable is empty
if (empty($playlist_id) || empty($song_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Playlist ID and Song ID are required.']);
    exit;
}

// Sanitize inputs to prevent SQL injection
$playlist_id = $con->real_escape_string($playlist_id);
$song_id = $con->real_escape_string($song_id);

// --- 1. Check if the combination already exists ---
$check_sql = "SELECT id FROM playlist_songs WHERE playlist_id = '$playlist_id' AND song_id = '$song_id'";
$result = $con->query($check_sql);

// --- 2. Decide what to do based on the result ---
if ($result->num_rows > 0) {
    // --- IT EXISTS: So, DELETE it ---
    $delete_sql = "DELETE FROM playlist_songs WHERE playlist_id = '$playlist_id' AND song_id = '$song_id'";
    
    if ($con->query($delete_sql) === TRUE) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Song removed from playlist!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error removing song: ' . $con->error
        ]);
    }

} else {
    // --- IT DOES NOT EXIST: So, INSERT it ---
    $insert_sql = "INSERT INTO playlist_songs (playlist_id, song_id) VALUES ('$playlist_id', '$song_id')";
    
    if ($con->query($insert_sql) === TRUE) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Song added to playlist!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error adding song: ' . $con->error
        ]);
    }
}

// 3. Close the database connection
$con->close();
?>