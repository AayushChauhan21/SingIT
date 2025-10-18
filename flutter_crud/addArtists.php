<?php

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
    $name = $_POST["name"];
    $photoTmp = $_FILES["photo"]["tmp_name"];

    try {
        $uploadResult = $cloudinary->uploadApi()->upload($photoTmp);
        $imageUrl = $uploadResult['secure_url'];

        $qry = "INSERT INTO artist (name,photo) VALUES ('$name','$imageUrl')";
        if (mysqli_query($con, $qry)) {
            // echo "<script>alert('Artist Inserted Successfully'); window.location.href='" . $baseUrl . "view_artist.php';</script>";
            $_SESSION['status'] = 'success';

            $_SESSION['message'] = 'Artist has been successfully inserted!';
            header("location:" . $baseUrl . "view_artist.php");
        } else {
            // echo "<script>alert('Oops... Something went wrong!'); window.history.back();</script>";
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Database insert failed. Please try again.';

            header("location:" . $baseUrl . "view_genres.php");
        }

    } catch (Exception $e) {
        // echo "<script>alert('Upload Failed: " . $e->getMessage() . "'); window.history.back();</script>";
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = $e->getMessage();

        header("location:" . $baseUrl . "view_genres.php");
    }
}

?>