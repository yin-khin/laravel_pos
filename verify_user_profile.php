<?php
// Script to verify user profile data in the database

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Profile;

echo "=== Verifying User Profile Data ===\n\n";

// Check if there are any users in the database
$userCount = User::count();
echo "Total users in database: " . $userCount . "\n";

if ($userCount > 0) {
    // Get all users with their profiles
    $users = User::with('profile')->get();
    
    foreach ($users as $user) {
        echo "\n--- User ID: " . $user->id . " ---\n";
        echo "Name: " . $user->name . "\n";
        echo "Email: " . $user->email . "\n";
        echo "User Type: " . $user->user_type . "\n";
        
        if ($user->profile) {
            echo "Profile ID: " . $user->profile->id . "\n";
            echo "Profile Phone: " . ($user->profile->phone ?? 'Not set') . "\n";
            echo "Profile Address: " . ($user->profile->address ?? 'Not set') . "\n";
            echo "Profile Image: " . ($user->profile->image ?? 'Not set') . "\n";
            echo "Profile Type: " . ($user->profile->type ?? 'Not set') . "\n";
        } else {
            echo "No profile found for this user.\n";
        }
    }
} else {
    echo "No users found in the database.\n";
}

echo "\n=== Verification Complete ===\n";