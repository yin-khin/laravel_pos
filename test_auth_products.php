<?php
// Test script to verify authentication and product fetching from API

echo "Testing authentication and product API endpoint...\n";

// First, let's login to get a token
$loginData = [
    'email' => 'test@example.com',
    'password' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:8000/api/auth/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$loginResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_error($ch)) {
    echo "Login cURL Error: " . curl_error($ch) . "\n";
    curl_close($ch);
    exit;
}

echo "Login HTTP Status Code: " . $httpCode . "\n";

if ($httpCode !== 200) {
    echo "Login failed. Response:\n";
    echo $loginResponse . "\n";
    curl_close($ch);
    exit;
}

$loginData = json_decode($loginResponse, true);
$token = $loginData['access_token'] ?? null;

if (!$token) {
    echo "Token not found in login response.\n";
    echo "Login response: " . $loginResponse . "\n";
    curl_close($ch);
    exit;
}

echo "Login successful. Token: " . $token . "\n";

// Now let's fetch products using the token
curl_close($ch);
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://localhost:8000/api/products");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);

$productsResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_error($ch)) {
    echo "Products cURL Error: " . curl_error($ch) . "\n";
} else {
    echo "Products HTTP Status Code: " . $httpCode . "\n";
    echo "Products Response:\n";
    echo $productsResponse . "\n";
}

curl_close($ch);
?>