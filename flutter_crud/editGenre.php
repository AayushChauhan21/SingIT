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
        // We handle this primarily in JS, but a server-side check is required
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Genre Name is required.';
        header("location:" . $baseUrl . "edit_genres.php?gid=" . $gid);
        exit;
    }

    // --- 2. Retrieve Current Image URL ---
    $currentDataQuery = mysqli_query($con, "SELECT image FROM genre WHERE gid=$gid");
    $currentData = mysqli_fetch_assoc($currentDataQuery);

    $imageUrl = $currentData['image'] ?? null; // Current image URL from DB

    // --- 3. Handle Cloudinary Upload ---
    if ($newImageTmp) {
        try {
            $uploadResult = $cloudinary->uploadApi()->upload($newImageTmp);
            $imageUrl = $uploadResult['secure_url']; // Overwrite old URL
        } catch (Exception $e) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = '❌ Image upload failed: ' . $e->getMessage();
            header("location:" . $baseUrl . "edit_genres.php?gid=" . $gid);
            exit;
        }
    }

    // --- 4. Prepare and Execute Database Update ---

    // Sanitization
    $name_safe = mysqli_real_escape_string($con, $name);
    $gid_safe = mysqli_real_escape_string($con, $gid);
    $imageUrl_safe = mysqli_real_escape_string($con, $imageUrl);

    // Update both name and image (image field holds either old URL or new Cloudinary URL)
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
    // Handle "View" button submission
    header("location:" . $baseUrl . "view_genres.php");
    exit;
} else {
    // If accessed without proper POST data
    header("location:" . $baseUrl . "view_genres.php");
    exit;
}
?>