<?php
include("connection.php");
header('Content-Type: application/json');

$arid = $_POST['arid'] ?? 0;

if ($arid) {
    $stmt = $con->prepare("DELETE FROM artist WHERE arid = ?");
    $stmt->bind_param("i", $arid); // 'i' for integer type
    $success = $stmt->execute();

    $rows_deleted = $stmt->affected_rows;
    $stmt->close();

    if ($success && $rows_deleted > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Artist deleted successfully! The list is refreshing.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Delete failed. Artist not found or database error.'
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing Artist ID.']);
}
?>