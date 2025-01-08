<?php
// Replace EXAMPLEKEY with your actual API key
$apiKey = 'EXAMPLEKEY';

// Ensure the API key is set
if (!isset($apiKey) || empty($apiKey)) {
        echo json_encode(['error' => 'API key is not configured']);
        exit;
}

// Determine the request type
if (isset($_GET['playlistId'])) {
    // Handle playlist requests
    $playlistId = $_GET['playlistId'];
    $pageToken = $_GET['pageToken'] ?? '';

    if (empty($playlistId)) {
        echo json_encode(['error' => 'Playlist ID is required']);
        exit;
    }

    // YouTube API URL for playlist items
    $apiUrl = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=$playlistId&maxResults=50&pageToken=$pageToken&key=$apiKey";

    // Fetch the API response
    $response = @file_get_contents($apiUrl);

    if ($response === false) {
        echo json_encode(['error' => 'Failed to fetch data from YouTube API for playlist']);
        exit;
    }

    // Pass the response back to the client
    header('Content-Type: application/json');
    echo $response;
} elseif (isset($_GET['videoIds'])) {
    // Handle video details requests
    $videoIds = $_GET['videoIds'];

    if (empty($videoIds)) {
        echo json_encode(['error' => 'Video IDs are required']);
        exit;
    }

    // Break the video IDs into chunks of 50 (YouTube API limit)
    $videoIdChunks = array_chunk(explode(',', $videoIds), 50);
    $results = [];

    foreach ($videoIdChunks as $chunk) {
        $ids = implode(',', $chunk);
        $apiUrl = "https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=$ids&key=$apiKey";

        $response = @file_get_contents($apiUrl);

        if ($response === false) {
            error_log("Failed to fetch data from YouTube API for video IDs: $ids");
            echo json_encode(['error' => 'Failed to fetch data from YouTube API for video details']);
            exit;
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE || isset($data['error'])) {
            error_log("YouTube API error: " . print_r($data['error'], true));
            echo json_encode(['error' => $data['error']['message'] ?? 'Unknown API error']);
            exit;
        }

        // Merge results
        $results = array_merge($results, $data['items']);
    }

    // Return combined results
    header('Content-Type: application/json');
    echo json_encode(['items' => $results]);
    exit;

} else {
        echo json_encode(['error' => 'Invalid request']);
        exit;
}
?>
