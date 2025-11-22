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

// Cloudinary Configuration (Using the credentials from your code)
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'do3hihcwa',
        'api_key' => '684953537186227',
        'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
    ]
]);

// Check for update action and required fields
if (isset($_POST["update"])) {
    $arid = $_POST["arid"] ?? null;
    $name = trim($_POST["name"] ?? '');
    $description = trim($_POST["description"] ?? '');

    // Get temporary file paths
    $newMainImageTmp = $_FILES["image"]["tmp_name"] ?? null;
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

    // --- 2. Retrieve Current Image URLs for Fallback/Deletion (Normal Way) ---
    // Fetch current data to use if no new image is uploaded.
    $currentDataQuery = mysqli_query($con, "SELECT photo, image FROM artist WHERE arid=$arid");
    $currentData = mysqli_fetch_assoc($currentDataQuery);

    $mainImageUrl = $currentData['photo'] ?? null;
    $transparentImageUrl = $currentData['image'] ?? null;

    // --- 3. Handle Cloudinary Uploads ---

    // A. Upload MAIN image if a new one is provided
    if ($newMainImageTmp) {
        try {
            $uploadResult = $cloudinary->uploadApi()->upload($newMainImageTmp);
            $mainImageUrl = $uploadResult['secure_url']; // Overwrite old URL

        } catch (Exception $e) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = '❌ Main image upload failed: ' . $e->getMessage();
            header("location:" . $baseUrl . "edit_artist.php?arid=" . $arid);
            exit;
        }
    }

    // B. Upload TRANSPARENT image if a new one is provided
    if ($newTransparentImageTmp) {
        try {
            $uploadResult = $cloudinary->uploadApi()->upload($newTransparentImageTmp);
            $transparentImageUrl = $uploadResult['secure_url']; // Overwrite old URL

        } catch (Exception $e) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = '❌ Transparent image upload failed: ' . $e->getMessage();
            header("location:" . $baseUrl . "edit_artist.php?arid=" . $arid);
            exit;
        }
    }

    // --- 4. Prepare and Execute Database Update (Update all fields) ---

    // Sanitization
    $name_safe = mysqli_real_escape_string($con, $name);
    $description_safe = mysqli_real_escape_string($con, $description);
    $arid_safe = mysqli_real_escape_string($con, $arid);

    // Since we fetch and maintain the old URLs if no new file is uploaded, 
    // we can update all columns every time.
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
    // Handle "View" button submission
    header("location:" . $baseUrl . "view_artist.php");
    exit;
} else {
    // If accessed without proper POST data
    header("location:" . $baseUrl . "view_artist.php");
    exit;
}
?>