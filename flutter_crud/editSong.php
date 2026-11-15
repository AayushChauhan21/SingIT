<?php
// editSong.php - API Handler

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

// 🔧 Utility Function: Cloudinary URL માંથી Public ID કાઢવા માટે (Delete કરવા માટે જરૂરી)
function getPublicIdFromUrl($url)
{
    if (empty($url))
        return null;

    // URL માંથી "upload/" પછીનો ભાગ મેળવો
    $path = parse_url($url, PHP_URL_PATH);
    $uploadPos = strpos($path, '/upload/');
    if ($uploadPos === false)
        return null;

    $relativePath = substr($path, $uploadPos + 8); // 'v1234567/Song/Vocals/filename.mp3'

    // જો વર્ઝન ફોલ્ડર હોય (v12345678) તો તે દૂર કરો
    $relativePath = preg_replace('/^v\d+\//', '', $relativePath);

    // એક્સ્ટેન્શન (.mp3, .jpg) દૂર કરો
    $publicId = pathinfo($relativePath, PATHINFO_DIRNAME) . '/' . pathinfo($relativePath, PATHINFO_FILENAME);

    // જો આગળ './' હોય તો કાઢી નાખો
    return ltrim($publicId, './');
}

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

    // 2. Collect/Sanitize POST Data
    $name = trim($_POST["trackName"] ?? '');
    $album = trim($_POST["albumName"] ?? '');
    $rawDuration = $_POST["duration"] ?? 0;
    $length = formatDuration((int) $rawDuration);
    $lyrics = $_POST["syncedLyrics"] ?? '';

    // Existing URLs from DB
    $vocalUrl = $song['vocal'];
    $instrumentalUrl = $song['instrumental'];
    $imageUrl = $song['image'];
    $posterUrl = $song['poster'];

    // Check Album Image URL updates (from Auto-fill hidden field)
    $formImageUrl = trim($_POST["albumImageUrl"] ?? '');
    if (!empty($formImageUrl) && $formImageUrl !== $song['image']) {
        // જો Auto-fill થી નવું ઈમેજ URL આવ્યું હોય અને જૂની ઈમેજ Cloudinary ની હોય, તો જૂની ઈમેજ ડિલીટ કરી દો
        $oldPublicId = getPublicIdFromUrl($song['image']);
        if ($oldPublicId) {
            try {
                $cloudinary->uploadApi()->destroy($oldPublicId, ['resource_type' => 'image']);
            } catch (Exception $e) {
            }
        }
        $imageUrl = $formImageUrl;
    }

    // Check for new file uploads
    $vocalAudioTmp = $_FILES["audio_vocal"]["tmp_name"] ?? '';
    $instTmp = $_FILES["audio_i"]["tmp_name"] ?? '';
    $manualTmp = $_FILES["manualImage"]["tmp_name"] ?? '';
    $posterTmp = $_FILES["posterImage"]["tmp_name"] ?? '';

    // 3. CLOUDINARY UPLOADS & OLD FILE REMOVAL
    try {
        // --- 1. Vocal Audio Update ---
        if (!empty($vocalAudioTmp)) {
            // જૂની Vocal ફાઈલ ડિલીટ કરો
            $oldPublicId = getPublicIdFromUrl($song['vocal']);
            if ($oldPublicId) {
                try {
                    $cloudinary->uploadApi()->destroy($oldPublicId, ['resource_type' => 'video']);
                } catch (Exception $e) {
                }
            }
            // નવી ફાઈલ Song/Vocals માં અપલોડ કરો
            $uploadResult = $cloudinary->uploadApi()->upload($vocalAudioTmp, [
                'resource_type' => 'video',
                'folder' => 'Song/Vocals'
            ]);
            $vocalUrl = $uploadResult['secure_url'];
        }

        // --- 2. Instrumental Audio Update ---
        if (!empty($instTmp)) {
            // જૂની Instrumental ફાઈલ ડિલીટ કરો
            $oldPublicId = getPublicIdFromUrl($song['instrumental']);
            if ($oldPublicId) {
                try {
                    $cloudinary->uploadApi()->destroy($oldPublicId, ['resource_type' => 'video']);
                } catch (Exception $e) {
                }
            }
            // નવી ફાઈલ Song/Instrumental માં અપલોડ કરો
            $uploadResult = $cloudinary->uploadApi()->upload($instTmp, [
                'resource_type' => 'video',
                'folder' => 'Song/Instrumental'
            ]);
            $instrumentalUrl = $uploadResult['secure_url'];
        }

        // --- 3. Album Image Update (Manual) ---
        if (!empty($manualTmp)) {
            // જૂની Album Image ડિલીટ કરો
            $oldPublicId = getPublicIdFromUrl($song['image']);
            if ($oldPublicId) {
                try {
                    $cloudinary->uploadApi()->destroy($oldPublicId, ['resource_type' => 'image']);
                } catch (Exception $e) {
                }
            }
            // નવી ફાઈલ Song/Image માં અપલોડ કરો
            $imgUpload = $cloudinary->uploadApi()->upload($manualTmp, [
                'folder' => 'Song/Image'
            ]);
            $imageUrl = $imgUpload['secure_url'];
        }

        // --- 4. Poster Image Update ---
        if (!empty($posterTmp)) {
            // જૂની Poster Image ડિલીટ કરો
            $oldPublicId = getPublicIdFromUrl($song['poster']);
            if ($oldPublicId) {
                try {
                    $cloudinary->uploadApi()->destroy($oldPublicId, ['resource_type' => 'image']);
                } catch (Exception $e) {
                }
            }
            // નવી ફાઈલ Song/Poster માં અપલોડ કરો
            $posterUpload = $cloudinary->uploadApi()->upload($posterTmp, [
                'folder' => 'Song/Poster'
            ]);
            $posterUrl = $posterUpload['secure_url'];
        }

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
            // 5. MAPPING UPDATES
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

            // Success Response
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Song has been successfully updated!';
            header("location:" . $baseUrl . "view_songs.php");
            exit;

        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Database update failed: ' . $stmt->error;
            header("location:" . $baseUrl . "view_songs.php");
            exit;
        }

        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Server Error (Cloudinary/General): ' . $e->getMessage();
        header("location:" . $baseUrl . "view_songs.php");
        exit;
    }
} else {
    header("location:" . $baseUrl . "view_songs.php");
    exit;
}
?>