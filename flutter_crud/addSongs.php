<?php

ini_set('max_execution_time', '600');

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
    ],
    'api' => [
        'connection_timeout' => 30,
        'upload_timeout' => 300
    ],
]);

// 🔧 Convert HH:MM:SS to seconds
function timeStringToSeconds($timeString)
{
    $parts = explode(':', $timeString);
    $parts = array_reverse($parts);
    $seconds = 0;
    if (isset($parts[0]))
        $seconds += (int) $parts[0];
    if (isset($parts[1]))
        $seconds += (int) $parts[1] * 60;
    if (isset($parts[2]))
        $seconds += (int) $parts[2] * 3600;
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

function selectedIds($field)
{
    $values = $_POST[$field] ?? [];
    $values = is_array($values) ? $values : [$values];
    $ids = [];

    foreach ($values as $value) {
        foreach (explode(',', (string) $value) as $id) {
            $id = (int) trim($id);
            if ($id > 0) {
                $ids[$id] = $id;
            }
        }
    }

    return array_values($ids);
}

if (isset($_POST["insert"])) {
    $name = $_POST["trackName"];
    $album = $_POST["albumName"];
    $rawDuration = trim($_POST["duration"] ?? '0');
    $length = formatDuration(timeStringToSeconds($rawDuration));
    $lyrics = $_POST["syncedLyrics"];
    $imageUrl = $_POST["albumImageUrl"];

    $vocalAudioTmp = $_FILES["audio_vocal"]["tmp_name"] ?? '';
    $instTmp = $_FILES["audio_i"]["tmp_name"] ?? '';
    $manualTmp = $_FILES["manualImage"]["tmp_name"] ?? '';
    $posterTmp = $_FILES["posterImage"]["tmp_name"] ?? '';
    $posterUrl = '';

    // Upload Vocal Audio -> Song/Vocals ફોલ્ડરમાં જશે
    if (!$vocalAudioTmp) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Vocal audio file is missing.';
        header("location:" . $baseUrl . "view_songs.php");
        exit;
    }

    try {
        // 1. Vocal Audio Upload -> Song/Vocals
        $uploadResult = $cloudinary->uploadApi()->upload($vocalAudioTmp, [
            'resource_type' => 'video',
            'folder' => 'Song/Vocals'
        ]);
        $vocalUrl = $uploadResult['secure_url'];
    } catch (Exception $e) {
        echo "<script>alert('❌ Vocal upload failed: " . $e->getMessage() . "');</script>";
        exit;
    }

    // Upload Instrumental Audio (Optional) -> Song/Instrumental ફોલ્ડરમાં જશે
    $instrumentalUrl = '';
    if ($instTmp) {
        try {
            // 2. Instrumental Audio Upload -> Song/Instrumental
            $uploadResult = $cloudinary->uploadApi()->upload($instTmp, [
                'resource_type' => 'video',
                'folder' => 'Song/Instrumental'
            ]);
            $instrumentalUrl = $uploadResult['secure_url'];
        } catch (Exception $e) {
            echo "<script>alert('❌ Instrumental upload failed: " . $e->getMessage() . "');</script>";
            exit;
        }
    }

    if ($manualTmp) {
        try {
            // 3. Manual Album Image Upload -> Song/Image
            $imgUpload = $cloudinary->uploadApi()->upload($manualTmp, [
                'folder' => 'Song/Image'
            ]);
            $imageUrl = $imgUpload['secure_url'];
        } catch (Exception $e) {
            echo "<script>alert('❌ Album Image upload failed: " . $e->getMessage() . "');</script>";
            exit;
        }
    } elseif (empty($imageUrl)) {
        // Jo manual file pan nathi ane auto-fill URL pan nathi, to default image set karo
        $imageUrl = $default_img_path;
    }

    // Upload Poster Image (Required) -> Song/Poster ફોલ્ડરમાં જશે
    if ($posterTmp) {
        try {
            // 4. Poster Image Upload -> Song/Poster
            $posterUpload = $cloudinary->uploadApi()->upload($posterTmp, [
                'folder' => 'Song/Poster'
            ]);
            $posterUrl = $posterUpload['secure_url'];
        } catch (Exception $e) {
            echo "<script>alert('❌ Poster Image upload failed: " . $e->getMessage() . "');</script>";
            exit;
        }
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Poster Image file is missing.';
        header("location:" . $baseUrl . "view_songs.php");
        exit;
    }

    // Main Song Insert Query (Prepared Statement)
    $qry = "INSERT INTO song (name, image, length, lyrics, album, instrumental, vocal, poster) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($qry);
    $stmt->bind_param("ssssssss", $name, $imageUrl, $length, $lyrics, $album, $instrumentalUrl, $vocalUrl, $posterUrl);

    if ($stmt->execute()) {
        $songId = $stmt->insert_id;

        // The form sends selected IDs as a comma-separated value in one array entry.
        $relations = [
            ['artist_song', 'artist_id', selectedIds('artistId')],
            ['genre_song', 'genre_id', selectedIds('genreId')],
            ['language_song', 'language_id', selectedIds('languageId')],
        ];

        foreach ($relations as [$table, $column, $ids]) {
            if (!$ids) {
                continue;
            }

            $mappingStmt = $con->prepare("INSERT INTO $table ($column, song_id) VALUES (?, ?)");
            foreach ($ids as $relationId) {
                $mappingStmt->bind_param("ii", $relationId, $songId);
                $mappingStmt->execute();
            }
            $mappingStmt->close();
        }

        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Song has been successfully inserted!';
        header("location:" . $baseUrl . "view_songs.php");
        exit;

    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Database insert failed. Error: ' . $stmt->error;
        header("location:" . $baseUrl . "view_songs.php");
        exit;
    }

    $stmt->close();
    $con->close();
}
?>