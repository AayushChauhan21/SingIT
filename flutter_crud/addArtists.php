<?php
// addArtists.php (સુધારેલો કોડ)

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
    // 1. Get all fields (including description and both files)
    $name = trim($_POST["name"] ?? '');
    $description = trim($_POST["description"] ?? '');
    $photoTmp = $_FILES["photo"]["tmp_name"] ?? null; // Main image
    $imageTransparentTmp = $_FILES["image"]["tmp_name"] ?? null; // Transparent image (name="image")

    $mainImageUrl = null;
    $transparentImageUrl = null;

    // Basic required field check (Validation is mainly done on the client side, but server check is good practice)
    if (empty($name) || empty($description) || !$photoTmp || !$imageTransparentTmp) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'All fields (Name, Description, and both Images) are required.';
        header("location:" . $baseUrl . "add_artist.php"); // Redirect back to form
        exit;
    }

    try {
        // 2. Upload Main Image (photo)
        if ($photoTmp) {
            $uploadResult = $cloudinary->uploadApi()->upload($photoTmp);
            $mainImageUrl = mysqli_real_escape_string($con, $uploadResult['secure_url']);
        }

        // 3. Upload Transparent Image (image)
        if ($imageTransparentTmp) {
            $uploadResult = $cloudinary->uploadApi()->upload($imageTransparentTmp);
            $transparentImageUrl = mysqli_real_escape_string($con, $uploadResult['secure_url']);
        }

        // Sanitize other inputs
        $name_safe = mysqli_real_escape_string($con, $name);
        $description_safe = mysqli_real_escape_string($con, $description);

        // 4. Insert Query: Include 'description' and 'image_transparent' columns
        // Assuming your DB columns are 'name', 'photo', 'description', and 'image_transparent'
        $qry = "INSERT INTO artist (name, description, photo, image) 
                VALUES ('$name_safe', '$description_safe', '$mainImageUrl', '$transparentImageUrl')";

        if (mysqli_query($con, $qry)) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Artist has been successfully inserted!';
            header("location:" . $baseUrl . "view_artist.php");
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Database insert failed: ' . mysqli_error($con);
            header("location:" . $baseUrl . "view_artist.php"); // Or view_artist.php
        }

    } catch (Exception $e) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Upload Failed: ' . $e->getMessage();
        header("location:" . $baseUrl . "view_artist.php"); // Or view_artist.php
    }
    exit;
}

?>