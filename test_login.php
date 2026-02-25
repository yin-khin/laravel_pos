<?php
// Script to test user login and get a valid token

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use Tymon\JWTAuth\Facades\JWTAuth;

echo "=== Testing User Login ===\n\n";

// Test credentials
$email = 'test@example.com';
$password = 'password123';

try {
    // Attempt to authenticate and get token
    $token = JWTAuth::attempt(['email' => $email, 'password' => $password]);
    
    if (!$token) {
        echo "❌ Login failed: Invalid credentials\n";
        exit;
    }
    
    echo "✅ Login successful!\n";
    echo "Token: $token\n";
    
    // Get the authenticated user
    $user = JWTAuth::toUser($token);
    echo "User ID: " . $user->id . "\n";
    echo "User Name: " . $user->name . "\n";
    echo "User Email: " . $user->email . "\n";
    echo "User Type: " . $user->user_type . "\n";
    
} catch (Exception $e) {
    echo "❌ Exception during login: " . $e->getMessage() . "\n";
}

echo "\n=== Login Test Complete ===\n";