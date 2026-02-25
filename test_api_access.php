<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

echo "=== Testing API Access ===\n\n";

// Test credentials
$email = 'test@example.com';
$password = 'password123';

try {
    // Get token
    $token = JWTAuth::attempt(['email' => $email, 'password' => $password]);
    
    if (!$token) {
        echo "❌ Login failed: Invalid credentials\n";
        exit;
    }
    
    echo "✅ Login successful!\n";
    echo "Token: $token\n\n";
    
    // Test accessing products endpoint
    echo "Testing products endpoint...\n";
    
    // Create a request to test the products endpoint
    $request = Request::create('/api/products', 'GET');
    $request->headers->set('Authorization', 'Bearer ' . $token);
    
    // Handle the request through the Laravel application
    $response = $app->handle($request);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "❌ Exception during API access test: " . $e->getMessage() . "\n";
}

echo "\n=== API Access Test Complete ===\n";