<?php
include("connection.php");
require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

header('Content-Type: application/json');

$gid = $_POST['gid'] ?? 0;

if ($gid) {
    $stmt_img = $con->prepare("SELECT image FROM genre WHERE gid = ?");
    $stmt_img->bind_param("i", $gid);
    $stmt_img->execute();
    $result = $stmt_img->get_result();
    $genre_row = $result->fetch_assoc();
    $stmt_img->close();

    $imageUrl = $genre_row['image'] ?? '';

    $stmt = $con->prepare("DELETE FROM genre WHERE gid = ?");
    $stmt->bind_param("i", $gid);
    $success = $stmt->execute();
    $rows_deleted = $stmt->affected_rows;
    $stmt->close();

    if ($success && $rows_deleted > 0) {
        if (!empty($imageUrl)) {
            try {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => 'do3hihcwa',
                        'api_key' => '684953537186227',
                        'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
                    ]
                ]);

                $parsedUrl = parse_url($imageUrl, PHP_URL_PATH);

                if (strpos($parsedUrl, 'upload/') !== false) {
                    $parts = explode('upload/', $parsedUrl);
                    $filePart = $parts[1] ?? '';
                    $filePart = preg_replace('/^v\d+\//', '', $filePart);

                    $publicId = pathinfo($filePart, PATHINFO_DIRNAME) . '/' . pathinfo($filePart, PATHINFO_FILENAME);
                    $publicId = trim($publicId, '/');

                    $cloudinary->uploadApi()->destroy($publicId);
                }
            } catch (Exception $e) {
                error_log("Cloudinary Delete Failed: " . $e->getMessage());
            }
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Genre and Image deleted successfully! The list is refreshing.'
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