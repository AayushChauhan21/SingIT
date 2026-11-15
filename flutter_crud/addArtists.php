<?php
// addArtists.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include("connection.php");

require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

// Dynamically build base URL
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$baseUrl .= "://" . $_SERVER['HTTP_HOST'] . "/SingIT/admin/";

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'do3hihcwa',
        'api_key' => '684953537186227',
        'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
    ]
]);

if (isset($_POST["insert"])) {
    // 1. Get all fields
    $name = trim($_POST["name"] ?? '');
    $description = trim($_POST["description"] ?? '');
    $photoTmp = $_FILES["photo"]["tmp_name"] ?? null; // Main image with background
    $imageTransparentTmp = $_FILES["image"]["tmp_name"] ?? null; // Transparent image without background

    $mainImageUrl = null;
    $transparentImageUrl = null;

    // Basic required field check
    if (empty($name) || empty($description) || !$photoTmp || !$imageTransparentTmp) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'All fields (Name, Description, and both Images) are required.';
        header("location:" . $baseUrl . "add_artist.php");
        exit;
    }

    try {
        // 📁 2. Upload Main Image -> 'Artist/with-bg' ફોલ્ડરમાં જશે
        if ($photoTmp) {
            $uploadResult = $cloudinary->uploadApi()->upload($photoTmp, [
                'folder' => 'Artist/with-bg'
            ]);
            $mainImageUrl = mysqli_real_escape_string($con, $uploadResult['secure_url']);
        }

        // 📁 3. Upload Transparent Image -> 'Artist/without-bg' ફોલ્ડરમાં જશે
        if ($imageTransparentTmp) {
            $uploadResult = $cloudinary->uploadApi()->upload($imageTransparentTmp, [
                'folder' => 'Artist/without-bg'
            ]);
            $transparentImageUrl = mysqli_real_escape_string($con, $uploadResult['secure_url']);
        }

        // Sanitize inputs
        $name_safe = mysqli_real_escape_string($con, $name);
        $description_safe = mysqli_real_escape_string($con, $description);

        // 4. Insert Query
        $qry = "INSERT INTO artist (name, description, photo, image) 
                VALUES ('$name_safe', '$description_safe', '$mainImageUrl', '$transparentImageUrl')";

        if (mysqli_query($con, $qry)) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Artist has been successfully inserted!';
            header("location:" . $baseUrl . "view_artist.php");
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Database insert failed: ' . mysqli_error($con);
            header("location:" . $baseUrl . "view_artist.php");
        }

    } catch (Exception $e) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Upload Failed: ' . $e->getMessage();
        header("location:" . $baseUrl . "view_artist.php");
    }
    exit;
}
?>