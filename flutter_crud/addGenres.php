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

        $qry = "INSERT INTO genre (name,image) VALUES ('$name','$imageUrl')";
        if (mysqli_query($con, $qry)) {
            $_SESSION['status'] = 'success';

            $_SESSION['message'] = 'Genre has been successfully inserted!';
            header("location:" . $baseUrl . "view_genres.php");

            // echo "<script>alert('Genre Inserted Successfully'); window.location.href='" . $baseUrl . "view_genres.php';</script>";
        } else {

            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Database insert failed. Please try again.';

            header("location:" . $baseUrl . "view_genres.php");

            // echo "<script>alert('Oops... Something went wrong!'); window.history.back();</script>";
        }

    } catch (Exception $e) {
        // echo "<script>alert('Upload Failed: " . $e->getMessage() . "'); window.history.back();</script>";
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = $e->getMessage();

        header("location:" . $baseUrl . "view_genres.php");

    }

}

// if (isset($_POST["view"])) {
//     echo "<script>window.location.href='" . $baseUrl . "view_genres.php';</script>";
//     exit;
// }


?>