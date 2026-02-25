<?php

// Simple script to test the profile API endpoint
echo "Testing Profile API Endpoint\n";

// Get the token from command line argument or use a default one
$token = $argv[1] ?? 'your_test_token_here';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:8000/api/auth/profile");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $token,
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: " . $http_code . "\n";
echo "Response:\n" . $response . "\n";

// Try to parse JSON
$json = json_decode($response, true);
if ($json) {
    echo "Parsed JSON:\n";
    print_r($json);
} else {
    echo "Invalid JSON response\n";
}