<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include("connection.php");
require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

// Base URL calculation
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$baseUrl .= "://" . $_SERVER['HTTP_HOST'] . "/SingIT/admin/";

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'do3hihcwa',
        'api_key' => '684953537186227',
        'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
    ]
]);

// 🔧 Convert HH:MM:SS to seconds
function timeStringToSeconds($timeString)
{
    $parts = explode(':', $timeString);
    $parts = array_reverse($parts); // Handle MM:SS or HH:MM:SS

    $seconds = 0;
    if (isset($parts[0]))
        $seconds += (int) $parts[0];      // Seconds
    if (isset($parts[1]))
        $seconds += (int) $parts[1] * 60;    // Minutes
    if (isset($parts[2]))
        $seconds += (int) $parts[2] * 3600;  // Hours

    return $seconds;
}

// 🔧 Convert seconds to HH:MM:SS
function formatDuration($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
}

if (isset($_POST["insert"])) {
    $name = $_POST["trackName"];
    $album = $_POST["albumName"];
    $rawDuration = trim($_POST["duration"] ?? '0');

    // Convert to seconds then format as HH:MM:SS
    $length = formatDuration(timeStringToSeconds($rawDuration));

    $lyrics = $_POST["syncedLyrics"];
    $imageUrl = $_POST["albumImageUrl"];
    $audioTmp = $_FILES["audio_v"]["tmp_name"];
    $instTmp = $_FILES["audio_i"]["tmp_name"];
    $manualTmp = $_FILES["manualImage"]["tmp_name"];

    // Upload Vocal Audio
    try {
        $uploadResult = $cloudinary->uploadApi()->upload($audioTmp, ['resource_type' => 'video']);
        $vocalUrl = $uploadResult['secure_url'];
    } catch (Exception $e) {
        echo "<script>alert('❌ Vocal upload failed: " . $e->getMessage() . "');</script>";
        exit;
    }

    // Upload Instrumental Audio (Optional)
    $instrumentalUrl = '';
    if ($instTmp) {
        try {
            $uploadResult = $cloudinary->uploadApi()->upload($instTmp, ['resource_type' => 'video']);
            $instrumentalUrl = $uploadResult['secure_url'];
        } catch (Exception $e) {
            echo "<script>alert('❌ Instrumental upload failed: " . $e->getMessage() . "');</script>";
            exit;
        }
    }

    // Upload Album Image (Manual)
    if (!$imageUrl && $manualTmp) {
        try {
            $imgUpload = $cloudinary->uploadApi()->upload($manualTmp);
            $imageUrl = $imgUpload['secure_url'];
        } catch (Exception $e) {
            echo "<script>alert('❌ Image upload failed: " . $e->getMessage() . "');</script>";
            exit;
        }
    }

    // Main Song Insert Query (Prepared Statement)
    $qry = "INSERT INTO song (name, image, length, lyrics, album, instrumental, vocal)
             VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($qry);
    $stmt->bind_param("sssssss", $name, $imageUrl, $length, $lyrics, $album, $instrumentalUrl, $vocalUrl);

    if ($stmt->execute()) {
        $songId = $stmt->insert_id;

        // --- MAPPING (Artist and Genre) ---
        // Note: For best practice, use Prepared Statements here too, but retaining mysqli_query for simplicity
        if (!empty($_POST['artistId'])) {
            foreach ($_POST['artistId'] as $artistId) {
                mysqli_query($con, "INSERT INTO artist_song (artist_id, song_id) VALUES ('$artistId', '$songId')");
            }
        }

        if (!empty($_POST['genreId'])) {
            foreach ($_POST['genreId'] as $genreId) {
                mysqli_query($con, "INSERT INTO genre_song (genre_id, song_id) VALUES ('$genreId', '$songId')");
            }
        }

        if (!empty($_POST['languageId'])) {
            foreach ($_POST['languageId'] as $languageId) {
                mysqli_query($con, "INSERT INTO language_song (language_id, song_id) VALUES ('$languageId', '$songId')");
            }
        }

        // --- SweetAlert Success Message (v1 Syntax) ---
        // Isse SweetAlert message dikhega aur OK click hone par redirect hoga.
        // SweetAlert Success Message (v1 Syntax)

        $_SESSION['status'] = 'success';

        $_SESSION['message'] = 'Song has been successfully inserted!';
        header("location:" . $baseUrl . "view_songs.php");


    } else {
        // Database Execution Failed

        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Database insert failed. Please try again.';

        header("location:" . $baseUrl . "view_songs.php");

        // echo "<script>alert('❌ Oops... Database insert failed!'); window.history.back();</script>";
    }

    $stmt->close();
    $con->close();
}
?>