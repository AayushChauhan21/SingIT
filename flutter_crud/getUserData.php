<?php
header('Content-Type: application/json');
require 'connection.php'; // This file should contain your $conn

$response = ['success' => false];

// 1. Check if the User ID (uid) was sent in the URL
if (isset($_GET['uid'])) {
    
    $userId = $_GET['uid'];

    $sql = "SELECT id, name, email, photo FROM user WHERE id = $userId";
    
    // 4. Execute the query
    $result = $con->query($sql);

    // 5. Check if the query was successful
    if ($result) {
        // 6. Check if a user was found (if rows were returned)
        if ($result->num_rows > 0) {
            
            // 7. Fetch the user data
            $user = $result->fetch_assoc();
            
            // User found
            $response['success'] = true;
            $response['user'] = $user;
        } else {
            // User not found
            $response['error'] = 'User not found';
        }
    } else {
        // SQL query failed
        $response['error'] = 'SQL query failed: ' . $con->error;
    }
    
} else {
    // 8. No User ID was provided
    $response['error'] = 'No user ID provided';
}

// 9. Close the database connection
$con->close();

// 10. Send the JSON response back to Flutter
echo json_encode($response);

?>