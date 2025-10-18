<?php
include("connection.php");
header('Content-Type: application/json');

$gid = $_POST['gid'] ?? 0;

if ($gid) {
    $stmt = $con->prepare("DELETE FROM genre WHERE gid = ?");
    $stmt->bind_param("i", $gid); // 'i' for integer type
    $success = $stmt->execute();

    $rows_deleted = $stmt->affected_rows;
    $stmt->close();

    if ($success && $rows_deleted > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Genre deleted successfully! The list is refreshing.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Delete failed. Genre not found or database error.'
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing Genre ID.']);
}
?>