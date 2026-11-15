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
        // અહીં આપણે 'folder' ઓપ્શન ઉમેર્યો છે જેથી ફોટો 'Genres' ફોલ્ડરમાં જશે
        $uploadResult = $cloudinary->uploadApi()->upload($photoTmp, [
            'folder' => 'Genres'
        ]);

        $imageUrl = $uploadResult['secure_url'];

        // SQL Injection થી બચવા માટે mysqli_real_escape_string વાપરવું હિતાવહ છે
        $name = mysqli_real_escape_string($con, $name);
        $imageUrl = mysqli_real_escape_string($con, $imageUrl);

        $qry = "INSERT INTO genre (name,image) VALUES ('$name','$imageUrl')";
        if (mysqli_query($con, $qry)) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Genre has been successfully inserted!';
            header("location:" . $baseUrl . "view_genres.php");
            exit; // header() પછી exit લખવું જરૂરી છે
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Database insert failed. Please try again.';
            header("location:" . $baseUrl . "view_genres.php");
            exit;
        }

    } catch (Exception $e) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = $e->getMessage();
        header("location:" . $baseUrl . "view_genres.php");
        exit;
    }
}
?>