<?php
// Replace EXAMPLEKEY with your actual API key
$apiKey = 'EXAMPLEKEY';

// Start the session if not already active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Ensure the API key is set
if (!isset($apiKey) || empty($apiKey)) {
    echo json_encode(['error' => 'API key is not configured']);
    exit;
}

// Determine the request type
if (isset($_GET['playlistId'])) {
    // Handle playlist requests
    $playlistId = $_GET['playlistId'];
    $pageToken = '';
    $etagKey = "etag_$playlistId";
    $cachedDataKey = "data_$playlistId";
    $storedEtag = $_SESSION[$etagKey] ?? null;
    $allItems = []; // To collect all playlist items across pages

    // properly get/handle duration with etag stuff 1/2
    function parseDuration($duration) {
        $interval = new DateInterval($duration);
        $parts = [];
        if ($interval->h > 0) {
            $parts[] = $interval->h . 'h';
        }
        if ($interval->i > 0) {
            $parts[] = $interval->i . 'm';
        }
        if ($interval->s > 0) {
            $parts[] = $interval->s . 's';
        }
        return implode(' ', $parts);
    }

    do {
        // Construct the API URL
        $apiUrl = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails&playlistId=$playlistId&maxResults=50&pageToken=$pageToken&key=$apiKey";

        // Set headers for the request
        $options = [
            'http' => [
                'header' => $storedEtag ? "If-None-Match: $storedEtag\r\n" : "",
            ],
        ];
        $context = stream_context_create($options);

        // Fetch the API response
        $response = @file_get_contents($apiUrl, false, $context);

        // Handle HTTP 304 (Not Modified)
        if ($http_response_header && strpos($http_response_header[0], '304') !== false) {
            $response = $_SESSION[$cachedDataKey] ?? null;
            if (empty($response)) {
                // file_put_contents('debug_log.txt', "ETag indicates 304 but cached data is missing. Fetching fresh data.\n\n", FILE_APPEND);
                $response = @file_get_contents($apiUrl); // Force a fresh request
            }
        }

        // Check for response errors
        if ($response === false || empty($response)) {
            // file_put_contents('debug_log.txt', "Failed or Empty API response\n\n", FILE_APPEND);
            break;
        }

        // Decode the response
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // file_put_contents('debug_log.txt', "JSON Decode Error: " . json_last_error_msg() . "\n\n", FILE_APPEND);
            break;
        }

        // Check for errors in the API response
        if (isset($data['error'])) {
            echo json_encode(['error' => $data['error']['message'] ?? 'Unknown API error']);
            exit;
        }

        // Collect items from this page
        $allItems = array_merge($allItems, $data['items']);

        // Update the next page token
        $pageToken = $data['nextPageToken'] ?? null;

        // Store ETag and response in the session
        foreach ($http_response_header as $header) {
            if (stripos($header, 'etag:') === 0) {
                $_SESSION[$etagKey] = trim(substr($header, 5));
                break;
            }
        }
        $_SESSION[$cachedDataKey] = $response;

        // Debugging
        // file_put_contents('debug_log.txt', "Processed page with token: $pageToken\n\n", FILE_APPEND);

    } while (!empty($pageToken));

    // Process all collected items for readable duration
    foreach ($allItems as &$item) {
        if (isset($item['contentDetails']['duration'])) {
            $item['durationReadable'] = parseDuration($item['contentDetails']['duration']);
        } else {
            $item['durationReadable'] = 'Unknown';
        }
    }

    // Return all collected items
    header('Content-Type: application/json');
    echo json_encode(['items' => $allItems]);
    // file_put_contents('debug_log.txt', print_r($allItems, true), FILE_APPEND);

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
