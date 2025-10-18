<?php
header('Content-Type: application/json');

$artist = $_GET['artist'] ?? '';
$track = $_GET['track'] ?? '';

if (!$artist || !$track) {
    echo json_encode(["status" => "error", "message" => "Missing artist or track"]);
    exit;
}

// === 1. Fetch lyrics from lrclib.net ===
$lyricsUrl = "https://lrclib.net/api/get?artist_name=" . urlencode($artist) . "&track_name=" . urlencode($track);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $lyricsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$lyricsResponse = curl_exec($ch);
curl_close($ch);
$lyricsData = json_decode($lyricsResponse, true);

// Check if lyrics found
if (empty($lyricsData['plainLyrics'])) {
    echo json_encode(["status" => "not_found", "message" => "No data found. Please enter manually."]);
    exit;
}

// === 2. Fetch album image from Spotify ===
$clientId = '4d5c3ee0b6b4478cabb67b5ddbac9fe7';
$clientSecret = '3fe5348f90e847f8a28954f430b5bed4';

function getSpotifyAccessToken($clientId, $clientSecret)
{
    $url = "https://accounts.spotify.com/api/token";
    $headers = [
        "Authorization: Basic " . base64_encode($clientId . ":" . $clientSecret),
        "Content-Type: application/x-www-form-urlencoded"
    ];
    $postFields = "grant_type=client_credentials";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($response, true);
    return $json['access_token'] ?? null;
}

$albumImage = '';
$accessToken = getSpotifyAccessToken($clientId, $clientSecret);

if ($accessToken) {
    $searchUrl = "https://api.spotify.com/v1/search?q=" . urlencode($track . ' ' . $artist) . "&type=track&limit=1";
    $headers = ["Authorization: Bearer $accessToken"];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $searchUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $spotifyData = json_decode($response, true);

    if (!empty($spotifyData['tracks']['items'][0]['album']['images'][0]['url'])) {
        $albumImage = $spotifyData['tracks']['items'][0]['album']['images'][0]['url'];
    }
}

// === Final response ===
echo json_encode([
    "status" => "found",
    "albumName" => $lyricsData['albumName'] ?? 'Unknown',
    "duration" => $lyricsData['duration'] ?? 0,
    // "plainLyrics" => $lyricsData['plainLyrics'],
    "syncedLyrics" => $lyricsData['syncedLyrics'] ?? '',
    "albumImage" => $albumImage
]);
?>