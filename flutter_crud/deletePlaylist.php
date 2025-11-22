<?php
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_GET['playlist_id']) || empty($_GET['playlist_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Playlist ID is required.']);
    exit;
}

$playlist_id = $_GET['playlist_id'];

// 2. Create the SQL query
$sql = "DELETE FROM playlists WHERE id = '$playlist_id'";

// 3. Execute the query
if ($con->query($sql) === TRUE) {
    
    // Check if any row was actually deleted
    if ($con->affected_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Playlist deleted successfully!'
        ]);
    } else {
        // No playlist was found with that ID
        echo json_encode([
            'status' => 'error',
            'message' => 'Playlist not found or already deleted.'
        ]);
    }

} else {
    // Other database error
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $con->error
    ]);
}

// 4. Close the connection
$con->close();
?>