<?php
include("connection.php");
require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

header('Content-Type: application/json');

$sid = $_POST['sid'] ?? 0;

if ($sid) {
    $stmt_assets = $con->prepare("SELECT image, poster, instrumental, vocal FROM song WHERE sid = ?");
    $stmt_assets->bind_param("i", $sid);
    $stmt_assets->execute();
    $result = $stmt_assets->get_result();
    $song_row = $result->fetch_assoc();
    $stmt_assets->close();

    $imageUrl = $song_row['image'] ?? '';
    $posterUrl = $song_row['poster'] ?? '';
    $instrumentalUrl = $song_row['instrumental'] ?? '';
    $vocalUrl = $song_row['vocal'] ?? '';

    $stmt = $con->prepare("DELETE FROM song WHERE sid = ?");
    $stmt->bind_param("i", $sid);

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

                $deleteFromCloudinary = function ($url, $resourceType = 'image') use ($cloudinary) {
                    if (!empty($url)) {
                        $parsedUrl = parse_url($url, PHP_URL_PATH);
                        if (strpos($parsedUrl, 'upload/') !== false) {
                            $parts = explode('upload/', $parsedUrl);
                            $filePart = preg_replace('/^v\d+\//', '', $parts[1] ?? '');
                            $publicId = pathinfo($filePart, PATHINFO_DIRNAME) . '/' . pathinfo($filePart, PATHINFO_FILENAME);
                            $publicId = trim($publicId, '/');

                            $cloudinary->uploadApi()->destroy($publicId, [
                                'resource_type' => $resourceType
                            ]);
                        }
                    }
                };

                // Images માટે resource_type 'image' રહેવા દેવું
                $deleteFromCloudinary($imageUrl, 'image');
                $deleteFromCloudinary($posterUrl, 'image');

                // Audio (Instrumental & Vocal) માટે resource_type 'video' હોવું જરૂરી છે
                $deleteFromCloudinary($instrumentalUrl, 'video');
                $deleteFromCloudinary($vocalUrl, 'video');

            } catch (Exception $e) {
                error_log("Cloudinary Delete Failed: " . $e->getMessage());
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Song and associated files deleted successfully! The list is refreshing.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Delete failed. Song not found or database error.'
            ]);
        }
    } catch (mysqli_sql_exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: This Song is linked elsewhere and cannot be deleted.'
        ]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing Song ID.']);
}
?>