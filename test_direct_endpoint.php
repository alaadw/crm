<?php
// Test the endpoint response directly
$url = 'http://127.0.0.1:8000/classes/moodle-courses';

echo "Testing endpoint: $url\n";
echo "========================================\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);

$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

echo "HTTP Status Code: $httpCode\n";
echo "Response Headers:\n";
echo $headers . "\n";
echo "========================================\n";
echo "Response Body:\n";
echo $body . "\n";
echo "========================================\n";

// Try to parse as JSON
$json = json_decode($body, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Parsed JSON:\n";
    echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "Not valid JSON: " . json_last_error_msg() . "\n";
}
?>
