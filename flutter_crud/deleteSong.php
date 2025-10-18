<?php
include("connection.php");
header('Content-Type: application/json');

$sid = $_POST['sid'] ?? 0;

if ($sid) {
    $stmt = $con->prepare("DELETE FROM song WHERE sid = ?");
    $stmt->bind_param("i", $sid); // 'i' for integer type
    $success = $stmt->execute();

    $rows_deleted = $stmt->affected_rows;
    $stmt->close();

    if ($success && $rows_deleted > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Song deleted successfully! The list is refreshing.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Delete failed. Song not found or database error.'
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing Song ID.']);
}
?>