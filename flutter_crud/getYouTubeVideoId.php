<?php
header('Content-Type: application/json');

// You must replace this with your actual YouTube Data API key
// For security, you should store this key in a more secure way,
// not directly in the file.
$apiKey = 'AIzaSyA0sMAaxPr_QMDzo1TunF2a68QQAfKTpMs';

// Get the search query from the Flutter app
$query = isset($_GET['q']) ? $_GET['q'] : '';

// Check if the query is empty
if (empty($query)) {
    echo json_encode(['error' => 'Query parameter "q" is missing.']);
    exit;
}

$url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($query) . "&type=video&maxResults=1&key=" . $apiKey;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);

$videoId = null;
if (isset($responseData['items'][0]['id']['videoId'])) {
    $videoId = $responseData['items'][0]['id']['videoId'];
}

echo json_encode(['videoId' => $videoId]);
?>
