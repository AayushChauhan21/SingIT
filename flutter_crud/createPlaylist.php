<?php
header('Content-Type: application/json');
include 'connection.php';

$user_id = $_GET['user_id'];
$name = $_GET['name'];

if ((empty($user_id)) && (empty($name))) {
    echo json_encode(['status' => 'error', 'message' => 'User ID and Playlist Name is required.']);
    exit;
}

$sql = "
    INSERT INTO playlists (user_id, name)
    VALUES ('$user_id', '$name')
";

// 2. Execute the query
if ($con->query($sql) === TRUE) {
    $new_playlist_id = $con->insert_id;

    // 4. Send success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Playlist created!',
        'playlist_id' => $new_playlist_id
    ]);
} else {
    if ($con->errno == 1062) {
        echo json_encode([
            'status' => 'error',
            'message' => 'You already have a playlist with this name.'
        ]);
    } else {
        // Other database error
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $con->error
        ]);
    }
}

// 6. Close the database connection
$con->close();
?>