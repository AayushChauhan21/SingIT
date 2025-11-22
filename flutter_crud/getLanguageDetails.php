<?php

header("Content-Type: application/json");
include 'connection.php';

$response = [
    'language_info' => null,
    'songs' => []
];

// 1. Get the language ID from the URL
if (isset($_GET['lid'])) {

    // --- IMPORTANT: Sanitize the ID to prevent SQL injection ---
    // Assuming $con is the mysqli connection object from connection.php
    $lid = mysqli_real_escape_string($con, $_GET['lid']);

    // --- QUERY 1: Get the Language's Details and Song Count ---

    $sql_language = "SELECT 
                        l.name, 
                        l.image, 
                        COUNT(ls.song_id) AS song_count
                    FROM 
                        language AS l
                    LEFT JOIN 
                        language_song AS ls ON l.lid = ls.language_id
                    WHERE 
                        l.lid = '$lid'
                    GROUP BY 
                        l.lid, l.name, l.image";

    $result_language = $con->query($sql_language);

    if ($result_language) {
        if ($result_language->num_rows > 0) {
            // Fetch the details and add them to the 'language_info' key
            $response['language_info'] = $result_language->fetch_assoc();
        }
    } else {
        $response['error'] = "Language query failed: " . $con->error;
        // Echo early if this critical part fails
        echo json_encode($response);
        $con->close();
        exit; // Stop the script
    }


    // --- QUERY 2: Get the Songs List associated with this Language ---

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
                        language_song AS ls ON s.sid = ls.song_id
                    WHERE 
                        ls.language_id = '$lid' 
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
    $response['error'] = "No language ID (lid) provided.";
}

// 5. Close connection and echo the combined JSON
$con->close();
echo json_encode($response);

?>