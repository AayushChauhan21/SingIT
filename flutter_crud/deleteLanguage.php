<?php
include("connection.php");

header('Content-Type: application/json');

$lid = $_POST['lid'] ?? 0;

if ($lid) {
    // Language Table માંથી Delete કરો
    $stmt = $con->prepare("DELETE FROM language WHERE lid = ?");
    $stmt->bind_param("i", $lid); // 'i' for integer type

    // SQL Execution Try કરો
    try {
        $success = $stmt->execute();
        $rows_deleted = $stmt->affected_rows;
        $stmt->close();

        if ($success && $rows_deleted > 0) {
            // સફળતાનું પરિણામ
            echo json_encode([
                'status' => 'success',
                'message' => 'Language deleted successfully! The list is refreshing.'
            ]);
        } else {
            // ડિલીટ નિષ્ફળ
            echo json_encode([
                'status' => 'error',
                'message' => 'Delete failed. Language not found or database error.'
            ]);
        }
    } catch (mysqli_sql_exception $e) {
        // Foreign Key Constraint Error Handling
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: This Language might be linked to songs and cannot be deleted.'
        ]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing Language ID.']);
}
?>