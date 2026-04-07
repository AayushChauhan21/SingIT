<?php
$conn = mysqli_connect(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASSWORD'), getenv('DB_NAME'));
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = file_get_contents('singit.sql');

if (mysqli_multi_query($conn, $sql)) {
    do {
        if ($result = mysqli_store_result($conn)) { mysqli_free_result($result); }
    } while (mysqli_more_results($conn) && mysqli_next_result($conn));
    
    echo "<h1>Database Imported Successfully</h1>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>