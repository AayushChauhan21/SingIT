<?php
include("connection.php");
header('Content-Type: application/json');

$id = $_POST['id'] ?? 0;

if ($id) {
    $stmt = $con->prepare("DELETE FROM slider WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();

    $rows_deleted = $stmt->affected_rows;
    $stmt->close();

    if ($success && $rows_deleted > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Slider entry deleted successfully! The list is refreshing.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Delete failed. Slider ID not found or database error.'
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing Slider ID.']);
}
?>