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

// Check for update action
if (isset($_POST["update"])) {
    $lid = $_POST["lid"] ?? null; // Using lid instead of gid
    $name = trim($_POST["name"] ?? '');

    // Get temporary file path
    $newImageTmp = $_FILES["image"]["tmp_name"] ?? null;

    // --- 1. Basic Validation ---
    if (!$lid || !is_numeric($lid)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Invalid Language ID provided.';
        header("location:" . $baseUrl . "view_languages.php"); // Redirect to view_languages
        exit;
    }

    if (empty($name)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Language Name is required.';
        header("location:" . $baseUrl . "edit_languages.php?lid=" . $lid); // Redirect to edit_languages with lid
        exit;
    }

    // --- 2. Retrieve Current Image URL ---
    // Fetch from 'language' table using 'lid'
    $currentDataQuery = mysqli_query($con, "SELECT image FROM language WHERE lid=$lid");
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
            header("location:" . $baseUrl . "edit_languages.php?lid=" . $lid); // Redirect to edit_languages with lid
            exit;
        }
    }

    // --- 4. Prepare and Execute Database Update ---

    // Sanitization
    $name_safe = mysqli_real_escape_string($con, $name);
    $lid_safe = mysqli_real_escape_string($con, $lid);
    $imageUrl_safe = mysqli_real_escape_string($con, $imageUrl);

    // Update 'language' table
    $qry = "UPDATE language SET 
                name='$name_safe', 
                image='$imageUrl_safe' 
            WHERE lid=$lid_safe";

    if (mysqli_query($con, $qry)) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Language has been successfully updated!';
        header("location:" . $baseUrl . "view_languages.php"); // Redirect to view_languages
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Database update failed: ' . mysqli_error($con);
        header("location:" . $baseUrl . "edit_languages.php?lid=" . $lid); // Redirect to edit_languages with lid
    }
    exit;

} elseif (isset($_POST["view"])) {
    // Handle "View" button submission
    header("location:" . $baseUrl . "view_languages.php"); // Redirect to view_languages
    exit;
} else {
    // If accessed without proper POST data
    header("location:" . $baseUrl . "view_languages.php"); // Redirect to view_languages
    exit;
}
?>