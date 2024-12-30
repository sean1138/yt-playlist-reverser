<?php
// Replace EXAMPLEKEY with your actual API key
$apiKey = 'EXAMPLEKEY';

// Get playlist ID and page token from the request
$playlistId = $_GET['playlistId'] ?? '';
$pageToken = $_GET['pageToken'] ?? '';

if (empty($playlistId)) {
    echo json_encode(['error' => 'Playlist ID is required']);
    exit;
}

// YouTube API URL
$apiUrl = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=$playlistId&maxResults=50&pageToken=$pageToken&key=$apiKey";

// Fetch the API response
$response = file_get_contents($apiUrl);

// Pass the response back to the client
header('Content-Type: application/json');
echo $response;
?>
