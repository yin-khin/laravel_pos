<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

// Test if the system routes are registered
$routes = Route::getRoutes();

echo "Total routes: " . count($routes) . "\n";

$foundSystemRoutes = false;
foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'system') !== false) {
        echo "Found system route: " . $uri . "\n";
        $foundSystemRoutes = true;
    }
}

if (!$foundSystemRoutes) {
    echo "No system routes found\n";
}

// Test the SystemStatusController directly
try {
    $controller = new \App\Http\Controllers\SystemStatusController();
    $request = new \Illuminate\Http\Request();
    
    // Test health check
    $response = $controller->healthCheck($request);
    echo "Health check response: " . json_encode($response->getData()) . "\n";
    
} catch (Exception $e) {
    echo "Error testing controller: " . $e->getMessage() . "\n";
}