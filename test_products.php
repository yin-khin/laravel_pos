<?php
// Test script to verify product fetching from API

echo "Testing product API endpoint...\n";

// Use cURL to make a request to the products endpoint
$ch = curl_init();

// Set the URL for the products endpoint
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/products');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for errors
if (curl_error($ch)) {
    echo 'cURL Error: ' . curl_error($ch) . "\n";
} else {
    echo 'HTTP Status Code: ' . $httpCode . "\n";
    echo "Response:\n";
    echo $response . "\n";
}

curl_close($ch);
?>