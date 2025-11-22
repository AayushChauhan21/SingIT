<?php
require "connection.php"; 
header('Content-Type: application/json');

$response = array("success" => false, "error" => "");

// 1. Check if the required data was sent from Flutter
if (isset($_POST['uid']) && isset($_POST['photo_url'])) {
    
    $uid = $_POST['uid'];
    $new_photo_url = $_POST['photo_url'];

    // 3. Build the SQL query string
    $sql = "UPDATE user SET photo = '$new_photo_url' WHERE id = '$uid'";

    // 4. Execute the query
    if ($con->query($sql) === TRUE) {
        
        // 5. Check if a row was actually updated
        if ($con->affected_rows > 0) {
            $response["success"] = true;
        } else {
            $response["error"] = "User not found and no change made.";
        }

    } else {
        // The query failed to run
        $response["error"] = "Database update failed: " . $con->error;
    }

} else {
    // uid or photo_url was not sent
    $response["error"] = "Required fields are missing (uid or photo_url).";
}

// 6. Close the connection and send the response
$con->close();
echo json_encode($response);
?>