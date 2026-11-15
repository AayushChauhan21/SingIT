<?php
include("connection.php");
require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

header('Content-Type: application/json');

$arid = $_POST['arid'] ?? 0;

if ($arid) {
    $stmt_img = $con->prepare("SELECT photo, image FROM artist WHERE arid = ?");
    $stmt_img->bind_param("i", $arid);
    $stmt_img->execute();
    $result = $stmt_img->get_result();
    $artist_row = $result->fetch_assoc();
    $stmt_img->close();

    $photoUrl = $artist_row['photo'] ?? '';
    $imageUrl = $artist_row['image'] ?? '';

    $stmt = $con->prepare("DELETE FROM artist WHERE arid = ?");
    $stmt->bind_param("i", $arid);

    try {
        $success = $stmt->execute();
        $rows_deleted = $stmt->affected_rows;
        $stmt->close();

        if ($success && $rows_deleted > 0) {
            try {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => 'do3hihcwa',
                        'api_key' => '684953537186227',
                        'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
                    ]
                ]);

                // ૧. મુખ્ય ફોટો (photo -> Artist/with-bg) ડિલીટ કરો
                if (!empty($photoUrl)) {
                    $parsedUrl1 = parse_url($photoUrl, PHP_URL_PATH);
                    if (strpos($parsedUrl1, 'upload/') !== false) {
                        $parts = explode('upload/', $parsedUrl1);
                        $filePart = preg_replace('/^v\d+\//', '', $parts[1] ?? '');
                        $publicId1 = pathinfo($filePart, PATHINFO_DIRNAME) . '/' . pathinfo($filePart, PATHINFO_FILENAME);
                        $publicId1 = trim($publicId1, '/');

                        $cloudinary->uploadApi()->destroy($publicId1);
                    }
                }

                // ૨. ટ્રાન્સપરન્ટ ફોટો (image -> Artist/without-bg) ડિલીટ કરો
                if (!empty($imageUrl)) {
                    $parsedUrl2 = parse_url($imageUrl, PHP_URL_PATH);
                    if (strpos($parsedUrl2, 'upload/') !== false) {
                        $parts = explode('upload/', $parsedUrl2);
                        $filePart = preg_replace('/^v\d+\//', '', $parts[1] ?? '');
                        $publicId2 = pathinfo($filePart, PATHINFO_DIRNAME) . '/' . pathinfo($filePart, PATHINFO_FILENAME);
                        $publicId2 = trim($publicId2, '/');

                        $cloudinary->uploadApi()->destroy($publicId2);
                    }
                }

            } catch (Exception $e) {
                error_log("Cloudinary Delete Failed: " . $e->getMessage());
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Artist and Images deleted successfully! The list is refreshing.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Delete failed. Artist not found or database error.'
            ]);
        }
    } catch (mysqli_sql_exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: This Artist might be linked to songs and cannot be deleted.'
        ]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing Artist ID.']);
}
?>