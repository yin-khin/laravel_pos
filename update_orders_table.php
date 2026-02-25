<?php
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configure database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['DB_HOST'],
    'database'  => $_ENV['DB_DATABASE'],
    'username'  => $_ENV['DB_USERNAME'],
    'password'  => $_ENV['DB_PASSWORD'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Modify the orders table to make cus_id nullable
try {
    Capsule::schema()->table('orders', function ($table) {
        $table->unsignedBigInteger('cus_id')->nullable()->change();
    });
    echo "Successfully updated orders table: cus_id is now nullable\n";
} catch (Exception $e) {
    echo "Error updating orders table: " . $e->getMessage() . "\n";
}