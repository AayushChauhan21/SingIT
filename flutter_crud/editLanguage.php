<?php
// FILE: editLanguage.php

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

    $relativePath = substr($path, $uploadPos + 8); // 'v1234567/Language/filename.jpg'

    // વર્ઝન ફોલ્ડર દૂર કરો (v12345678)
    $relativePath = preg_replace('/^v\d+\//', '', $relativePath);

    // એક્સ્ટેન્શન (.jpg, .png) દૂર કરો
    $publicId = pathinfo($relativePath, PATHINFO_DIRNAME) . '/' . pathinfo($relativePath, PATHINFO_FILENAME);

    return ltrim($publicId, './');
}

// Check for update action
if (isset($_POST["update"])) {
    $lid = $_POST["lid"] ?? null;
    $name = trim($_POST["name"] ?? '');

    // Get temporary file path
    $newImageTmp = $_FILES["photo"]["tmp_name"] ?? null;

    // --- 1. Basic Validation ---
    if (!$lid || !is_numeric($lid)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Invalid Language ID provided.';
        header("location:" . $baseUrl . "view_language.php");
        exit;
    }

    if (empty($name)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Language Name is required.';
        header("location:" . $baseUrl . "edit_language.php?lid=" . $lid);
        exit;
    }

    // --- 2. Retrieve Current Image URL ---
    $currentDataQuery = mysqli_query($con, "SELECT image FROM language WHERE lid=$lid");
    $currentData = mysqli_fetch_assoc($currentDataQuery);

    $imageUrl = $currentData['image'] ?? null; // Current image URL from DB

    // --- 3. Handle Cloudinary Upload & Old Image Removal ---
    if ($newImageTmp) {
        try {
            // 🗑️ ૧. જૂની ઈમેજ જો Cloudinary ની હોય તો તેને ડિલીટ કરો
            if (!empty($imageUrl)) {
                $oldPublicId = getPublicIdFromUrl($imageUrl);
                if ($oldPublicId) {
                    try {
                        $cloudinary->uploadApi()->destroy($oldPublicId, ['resource_type' => 'image']);
                    } catch (Exception $e) {
                        // Ignore error if old image delete fails
                    }
                }
            }

            // 📁 ૨. નવી ઈમેજ Cloudinary માં 'Language' ફોલ્ડરમાં અપલોડ કરો
            $uploadResult = $cloudinary->uploadApi()->upload($newImageTmp, [
                'folder' => 'Language'
            ]);
            $imageUrl = $uploadResult['secure_url']; // Overwrite with new URL

        } catch (Exception $e) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = '❌ Image upload failed: ' . $e->getMessage();
            header("location:" . $baseUrl . "edit_language.php?lid=" . $lid);
            exit;
        }
    }

    // --- 4. Prepare and Execute Database Update ---
    $name_safe = mysqli_real_escape_string($con, $name);
    $lid_safe = mysqli_real_escape_string($con, $lid);
    $imageUrl_safe = mysqli_real_escape_string($con, $imageUrl);

    $qry = "UPDATE language SET 
                name='$name_safe', 
                image='$imageUrl_safe' 
            WHERE lid=$lid_safe";

    if (mysqli_query($con, $qry)) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Language has been successfully updated!';
        header("location:" . $baseUrl . "view_language.php");
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Database update failed: ' . mysqli_error($con);
        header("location:" . $baseUrl . "edit_language.php?lid=" . $lid);
    }
    exit;

} elseif (isset($_POST["view"])) {
    header("location:" . $baseUrl . "view_language.php");
    exit;
} else {
    header("location:" . $baseUrl . "view_language.php");
    exit;
}
?>