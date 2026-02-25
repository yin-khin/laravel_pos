<?php

require_once 'vendor/autoload.php';

use App\Services\PaymentGatewayService;
use App\Services\AccountingIntegrationService;
use App\Services\EcommerceIntegrationService;
use App\Services\NotificationService;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "Testing Integration Services\n";
echo "========================\n\n";

// Test Payment Gateway Service
echo "1. Testing Payment Gateway Service\n";
try {
    $paymentService = new PaymentGatewayService();
    echo "   ✓ PaymentGatewayService instantiated successfully\n";
} catch (Exception $e) {
    echo "   ✗ PaymentGatewayService instantiation failed: " . $e->getMessage() . "\n";
}

// Test Accounting Integration Service
echo "\n2. Testing Accounting Integration Service\n";
try {
    $accountingService = new AccountingIntegrationService();
    echo "   ✓ AccountingIntegrationService instantiated successfully\n";
} catch (Exception $e) {
    echo "   ✗ AccountingIntegrationService instantiation failed: " . $e->getMessage() . "\n";
}

// Test E-commerce Integration Service
echo "\n3. Testing E-commerce Integration Service\n";
try {
    $ecommerceService = new EcommerceIntegrationService();
    echo "   ✓ EcommerceIntegrationService instantiated successfully\n";
} catch (Exception $e) {
    echo "   ✗ EcommerceIntegrationService instantiation failed: " . $e->getMessage() . "\n";
}

// Test Notification Service
echo "\n4. Testing Notification Service\n";
try {
    $notificationService = new NotificationService();
    echo "   ✓ NotificationService instantiated successfully\n";
} catch (Exception $e) {
    echo "   ✗ NotificationService instantiation failed: " . $e->getMessage() . "\n";
}

echo "\nIntegration services test completed.\n";
echo "Note: Actual API calls require valid credentials in the .env file.\n";