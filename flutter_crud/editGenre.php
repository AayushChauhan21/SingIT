<?php
// FILE: editGenre.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include connection
include("connection.php");

require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

// Dynamically build base URL for redirection
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$baseUrl .= "://" . $_SERVER['HTTP_HOST'] . "/SingIT/admin/";

// Cloudinary Configuration 
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'do3hihcwa',
        'api_key' => '684953537186227',
        'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
    ]
]);

// 🔧 Utility Function: Cloudinary URL માંથી Public ID કાઢવા માટે (Delete કરવા માટે જરૂરી)
function getPublicIdFromUrl($url)
{
    if (empty($url))
        return null;

    $path = parse_url($url, PHP_URL_PATH);
    $uploadPos = strpos($path, '/upload/');
    if ($uploadPos === false)
        return null;

    $relativePath = substr($path, $uploadPos + 8); // 'v1234567/Genres/filename.jpg'

    // વર્ઝન ફોલ્ડર દૂર કરો (v12345678)
    $relativePath = preg_replace('/^v\d+\//', '', $relativePath);

    // એક્સ્ટેન્શન (.jpg, .png) દૂર કરો
    $publicId = pathinfo($relativePath, PATHINFO_DIRNAME) . '/' . pathinfo($relativePath, PATHINFO_FILENAME);

    return ltrim($publicId, './');
}

// Check for update action
if (isset($_POST["update"])) {
    $gid = $_POST["gid"] ?? null;
    $name = trim($_POST["name"] ?? '');

    // Get temporary file path
    $newImageTmp = $_FILES["image"]["tmp_name"] ?? null;

    // --- 1. Basic Validation ---
    if (!$gid || !is_numeric($gid)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Invalid Genre ID provided.';
        header("location:" . $baseUrl . "view_genres.php");
        exit;
    }

    if (empty($name)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Genre Name is required.';
        header("location:" . $baseUrl . "edit_genres.php?gid=" . $gid);
        exit;
    }

    // --- 2. Retrieve Current Image URL ---
    $currentDataQuery = mysqli_query($con, "SELECT image FROM genre WHERE gid=$gid");
    $currentData = mysqli_fetch_assoc($currentDataQuery);

    // ડિફોલ્ટ તરીકે જૂની ઈમેજનું URL સેટ રાખીએ
    $imageUrl = $currentData['image'] ?? null;

    // --- 3. Handle Cloudinary Upload & Old Image Removal ---
    if (!empty($newImageTmp)) {
        try {
            // 🗑️ ૧. જો પહેલેથી ઈમેજ હોય, તો જૂની ઈમેજ Cloudinary માંથી ડિલીટ કરો
            if (!empty($imageUrl)) {
                $oldPublicId = getPublicIdFromUrl($imageUrl);
                if ($oldPublicId) {
                    try {
                        $cloudinary->uploadApi()->destroy($oldPublicId, ['resource_type' => 'image']);
                    } catch (Exception $e) {
                        // Old image deletion error can be ignored
                    }
                }
            }

            // 📁 ૨. નવી ઈમેજ Cloudinary ના 'Genres' ફોલ્ડરમાં અપલોડ કરો
            $uploadResult = $cloudinary->uploadApi()->upload($newImageTmp, [
                'folder' => 'Genres'
            ]);
            $imageUrl = $uploadResult['secure_url']; // નવા URL વડે અપડેટ કરો

        } catch (Exception $e) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = '❌ Image upload failed: ' . $e->getMessage();
            header("location:" . $baseUrl . "edit_genres.php?gid=" . $gid);
            exit;
        }
    }

    // --- 4. Prepare and Execute Database Update ---
    $name_safe = mysqli_real_escape_string($con, $name);
    $gid_safe = mysqli_real_escape_string($con, $gid);
    $imageUrl_safe = mysqli_real_escape_string($con, $imageUrl);

    // Update both name and image
    $qry = "UPDATE genre SET 
                name='$name_safe', 
                image='$imageUrl_safe' 
            WHERE gid=$gid_safe";

    if (mysqli_query($con, $qry)) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Genre has been successfully updated!';
        header("location:" . $baseUrl . "view_genres.php");
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Database update failed: ' . mysqli_error($con);
        header("location:" . $baseUrl . "edit_genres.php?gid=" . $gid);
    }
    exit;

} elseif (isset($_POST["view"])) {
    header("location:" . $baseUrl . "view_genres.php");
    exit;
} else {
    header("location:" . $baseUrl . "view_genres.php");
    exit;
}
?>