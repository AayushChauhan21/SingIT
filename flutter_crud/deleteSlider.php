<?php
// ખાતરી કરો કે connection.php માં $con વેરીએબલ યોગ્ય રીતે વ્યાખ્યાયિત થયેલ છે.
include("connection.php");
header('Content-Type: application/json');

// POST માંથી 'id' (Slider ID) મેળવો. જો ન મળે તો 0 સેટ કરો.
$id = $_POST['id'] ?? 0;

if ($id) {
    // SQL DELETE કમાન્ડ સ્લાઇડર ટેબલ પર 'id' નો ઉપયોગ કરીને
    $stmt = $con->prepare("DELETE FROM slider WHERE id = ?");

    // 'i' (integer) તરીકે id ને બાંધો
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();

    $rows_deleted = $stmt->affected_rows;
    $stmt->close();

    if ($success && $rows_deleted > 0) {
        // સફળતાપૂર્વક ડિલીટ થવા પર
        echo json_encode([
            'status' => 'success',
            'message' => 'Slider entry deleted successfully! The list is refreshing.'
        ]);
    } else {
        // ડિલીટ નિષ્ફળ થવા પર (દા.ત., ID ન મળે અથવા ડેટાબેઝ ભૂલ)
        echo json_encode([
            'status' => 'error',
            'message' => 'Delete failed. Slider ID not found or database error.'
        ]);
    }
} else {
    // જો ID ખૂટતી હોય અથવા અમાન્ય હોય
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing Slider ID.']);
}
?>