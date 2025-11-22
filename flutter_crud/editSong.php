<?php
// editSong.php - API Handler

// ⚠️ STEP 1: CRITICAL SERVER SETTINGS FOR LARGE FILES/CLOUDINARY
// Ensure these are high enough based on your file sizes (e.g., 256M, 300 seconds).
ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');
ini_set('post_max_size', '256M');
ini_set('upload_max_filesize', '256M');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include("connection.php");
require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

// Base URL calculation (REQUIRED for header redirect)
// **You MUST verify this path is correct for your 'view_songs.php' location.**
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
// Assumes 'editSong.php' is one directory deep (in flutter_crud) from the admin root
$baseUrl .= "://" . $_SERVER['HTTP_HOST'] . "/SIngIT/admin/";

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'do3hihcwa',
        'api_key' => '684953537186227',
        'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
    ]
]);

// --- Utility Functions (Time conversion) ---
function formatDuration($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
}


// --- UPDATE LOGIC ---
if (isset($_POST["update"])) {
    // ⚠️ Remove: header('Content-Type: application/json'); // Not needed for redirect flow

    $sid = $_POST['sid'] ?? 0;
    if (!$sid) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Invalid Song ID provided.';
        header("location:" . $baseUrl . "view_songs.php");
        exit;
    }

    // 1. Fetch Existing Data
    $songQuery = mysqli_query($con, "SELECT * FROM song WHERE sid = '$sid'");
    $song = mysqli_fetch_assoc($songQuery);

    if (!$song) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Song not found in database.';
        header("location:" . $baseUrl . "view_songs.php");
        exit;
    }

    // 2. Collect/Sanitize POST Data and Initialize URLs with EXISTING DB values
    $name = trim($_POST["trackName"] ?? '');
    $album = trim($_POST["albumName"] ?? '');
    $rawDuration = $_POST["duration"] ?? 0;
    $length = formatDuration((int) $rawDuration);
    $lyrics = $_POST["syncedLyrics"] ?? '';

    // Initialize URLs with existing DB values
    $vocalUrl = $song['vocal'];
    $instrumentalUrl = $song['instrumental'];
    $imageUrl = $song['image'];
    $posterUrl = $song['poster'];

    // Check Album Image URL updates (from Auto-fill hidden field)
    $formImageUrl = trim($_POST["albumImageUrl"] ?? '');
    if (!empty($formImageUrl)) {
        $imageUrl = $formImageUrl;
    }

    // Check for file uploads
    $vocalAudioTmp = $_FILES["audio_vocal"]["tmp_name"] ?? '';
    $instTmp = $_FILES["audio_i"]["tmp_name"] ?? '';
    $manualTmp = $_FILES["manualImage"]["tmp_name"] ?? '';
    $posterTmp = $_FILES["posterImage"]["tmp_name"] ?? '';

    // 3. CLOUDINARY UPLOADS
    try {
        // Vocal Audio: Replace existing URL ONLY if a new file exists
        if (!empty($vocalAudioTmp)) {
            $uploadResult = $cloudinary->uploadApi()->upload($vocalAudioTmp, ['resource_type' => 'video']);
            $vocalUrl = $uploadResult['secure_url'];
        }

        // Instrumental Audio: Replace existing URL ONLY if a new file exists
        if (!empty($instTmp)) {
            $uploadResult = $cloudinary->uploadApi()->upload($instTmp, ['resource_type' => 'video']);
            $instrumentalUrl = $uploadResult['secure_url'];
        }

        // Manual Image: Replace existing URL ONLY if a new file exists
        if (!empty($manualTmp)) {
            $imgUpload = $cloudinary->uploadApi()->upload($manualTmp);
            $imageUrl = $imgUpload['secure_url'];
        }

        // Poster Image: Replace existing URL ONLY if a new file exists
        if (!empty($posterTmp)) {
            $posterUpload = $cloudinary->uploadApi()->upload($posterTmp);
            $posterUrl = $posterUpload['secure_url'];
        }

        // --- Optional: Re-validation check for required fields if DB value was empty/null ---
        if (empty($vocalUrl)) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Vocal audio file is required for update.';
            header("location:" . $baseUrl . "view_songs.php");
            exit;
        }

        // 4. SQL UPDATE QUERY
        $qry = "UPDATE song SET name=?, image=?, length=?, lyrics=?, album=?, instrumental=?, vocal=?, poster=? WHERE sid=?";
        $stmt = $con->prepare($qry);

        $stmt->bind_param("ssssssssi", $name, $imageUrl, $length, $lyrics, $album, $instrumentalUrl, $vocalUrl, $posterUrl, $sid);

        if ($stmt->execute()) {
            // 5. MAPPING UPDATES (Delete old mappings and insert new ones)

            mysqli_query($con, "DELETE FROM artist_song WHERE song_id = '$sid'");
            mysqli_query($con, "DELETE FROM genre_song WHERE song_id = '$sid'");
            mysqli_query($con, "DELETE FROM language_song WHERE song_id = '$sid'");

            // Artist Mapping
            $artistIds = explode(',', $_POST['artistIdList'] ?? '');
            foreach ($artistIds as $artistId) {
                if (trim($artistId) !== '')
                    mysqli_query($con, "INSERT INTO artist_song (artist_id, song_id) VALUES ('" . (int) $artistId . "', '$sid')");
            }

            // Genre Mapping
            $genreIds = explode(',', $_POST['genreIdList'] ?? '');
            foreach ($genreIds as $genreId) {
                if (trim($genreId) !== '')
                    mysqli_query($con, "INSERT INTO genre_song (genre_id, song_id) VALUES ('" . (int) $genreId . "', '$sid')");
            }

            // Language Mapping
            $languageIds = explode(',', $_POST['languageIdList'] ?? '');
            foreach ($languageIds as $languageId) {
                if (trim($languageId) !== '')
                    mysqli_query($con, "INSERT INTO language_song (language_id, song_id) VALUES ('" . (int) $languageId . "', '$sid')");
            }

            // Success Response (Redirect)
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Song has been successfully updated!';
            header("location:" . $baseUrl . "view_songs.php");

        } else {
            // Database Error Response (Redirect)
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Database update failed: ' . $stmt->error;
            header("location:" . $baseUrl . "view_songs.php");
        }

        $stmt->close();
    } catch (Exception $e) {
        // Cloudinary/General Error Response (Redirect)
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Server Error (Cloudinary/General): ' . $e->getMessage();
        header("location:" . $baseUrl . "view_songs.php");
    }
} else {
    // If not a POST request, redirect back
    header("location:" . $baseUrl . "view_songs.php");
}
?>