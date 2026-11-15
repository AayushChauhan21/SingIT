<?php
// editArtist.php (API Endpoint for processing form submission)

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

// 🔧 Utility Function: Cloudinary URL માંથી Public ID કાઢવા માટે (Delete કરવા માટે)
function getPublicIdFromUrl($url)
{
    if (empty($url))
        return null;

    $path = parse_url($url, PHP_URL_PATH);
    $uploadPos = strpos($path, '/upload/');
    if ($uploadPos === false)
        return null;

    $relativePath = substr($path, $uploadPos + 8); // 'v1234567/Artist/with-bg/filename.jpg'

    // વર્ઝન ફોલ્ડર દૂર કરો (v12345678)
    $relativePath = preg_replace('/^v\d+\//', '', $relativePath);

    // એક્સ્ટેન્શન (.jpg, .png) દૂર કરો
    $publicId = pathinfo($relativePath, PATHINFO_DIRNAME) . '/' . pathinfo($relativePath, PATHINFO_FILENAME);

    return ltrim($publicId, './');
}

// Check for update action and required fields
if (isset($_POST["update"])) {
    $arid = $_POST["arid"] ?? null;
    $name = trim($_POST["name"] ?? '');
    $description = trim($_POST["description"] ?? '');

    // Get temporary file paths
    $newMainImageTmp = $_FILES["image"]["tmp_name"] ?? null; // HTML ફોર્મ મુજબ name="image" અથવા "photo" હોઈ શકે
    $newTransparentImageTmp = $_FILES["image_transparent"]["tmp_name"] ?? null;

    // --- 1. Basic Validation ---
    if (!$arid || !is_numeric($arid)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Invalid Artist ID provided.';
        header("location:" . $baseUrl . "view_artist.php");
        exit;
    }

    if (empty($name)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Artist Name is required.';
        header("location:" . $baseUrl . "edit_artist.php?arid=" . $arid);
        exit;
    }

    if (empty($description)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Artist Description is required.';
        header("location:" . $baseUrl . "edit_artist.php?arid=" . $arid);
        exit;
    }

    // --- 2. Retrieve Current Image URLs ---
    $currentDataQuery = mysqli_query($con, "SELECT photo, image FROM artist WHERE arid=$arid");
    $currentData = mysqli_fetch_assoc($currentDataQuery);

    $mainImageUrl = $currentData['photo'] ?? null;
    $transparentImageUrl = $currentData['image'] ?? null;

    // --- 3. Handle Cloudinary Uploads & Old File Deletion ---

    // A. Upload MAIN image if a new one is provided -> 'Artist/with-bg'
    if (!empty($newMainImageTmp)) {
        try {
            // જૂની Main Image ડિલીટ કરો
            if (!empty($mainImageUrl)) {
                $oldPublicId = getPublicIdFromUrl($mainImageUrl);
                if ($oldPublicId) {
                    try {
                        $cloudinary->uploadApi()->destroy($oldPublicId, ['resource_type' => 'image']);
                    } catch (Exception $e) {
                    }
                }
            }

            // નવી ફાઈલ 'Artist/with-bg' ફોલ્ડરમાં અપલોડ કરો
            $uploadResult = $cloudinary->uploadApi()->upload($newMainImageTmp, [
                'folder' => 'Artist/with-bg'
            ]);
            $mainImageUrl = $uploadResult['secure_url'];

        } catch (Exception $e) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = '❌ Main image upload failed: ' . $e->getMessage();
            header("location:" . $baseUrl . "edit_artist.php?arid=" . $arid);
            exit;
        }
    }

    // B. Upload TRANSPARENT image if a new one is provided -> 'Artist/without-bg'
    if (!empty($newTransparentImageTmp)) {
        try {
            // જૂની Transparent Image ડિલીટ કરો
            if (!empty($transparentImageUrl)) {
                $oldPublicId = getPublicIdFromUrl($transparentImageUrl);
                if ($oldPublicId) {
                    try {
                        $cloudinary->uploadApi()->destroy($oldPublicId, ['resource_type' => 'image']);
                    } catch (Exception $e) {
                    }
                }
            }

            // નવી ફાઈલ 'Artist/without-bg' ફોલ્ડરમાં અપલોડ કરો
            $uploadResult = $cloudinary->uploadApi()->upload($newTransparentImageTmp, [
                'folder' => 'Artist/without-bg'
            ]);
            $transparentImageUrl = $uploadResult['secure_url'];

        } catch (Exception $e) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = '❌ Transparent image upload failed: ' . $e->getMessage();
            header("location:" . $baseUrl . "edit_artist.php?arid=" . $arid);
            exit;
        }
    }

    // --- 4. Prepare and Execute Database Update ---
    $name_safe = mysqli_real_escape_string($con, $name);
    $description_safe = mysqli_real_escape_string($con, $description);
    $arid_safe = mysqli_real_escape_string($con, $arid);

    $mainImageUrl_safe = mysqli_real_escape_string($con, $mainImageUrl);
    $transparentImageUrl_safe = mysqli_real_escape_string($con, $transparentImageUrl);

    $qry = "UPDATE artist SET 
                name='$name_safe', 
                description='$description_safe', 
                photo='$mainImageUrl_safe',
                image='$transparentImageUrl_safe' 
            WHERE arid=$arid_safe";

    if (mysqli_query($con, $qry)) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Artist has been successfully updated!';
        header("location:" . $baseUrl . "view_artist.php");
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Database update failed: ' . mysqli_error($con);
        header("location:" . $baseUrl . "edit_artist.php?arid=" . $arid);
    }
    exit;

} elseif (isset($_POST["view"])) {
    header("location:" . $baseUrl . "view_artist.php");
    exit;
} else {
    header("location:" . $baseUrl . "view_artist.php");
    exit;
}
?>