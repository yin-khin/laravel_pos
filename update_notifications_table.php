<?php

// This script updates the notifications table structure to match the new requirements
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Updating Notifications Table ===\n\n";

try {
    // Check if the table exists
    if (!Schema::hasTable('notifications')) {
        echo "❌ Error: notifications table does not exist!\n";
        exit(1);
    }
    
    // Add missing columns if they don't exist
    if (!Schema::hasColumn('notifications', 'user_id')) {
        DB::statement('ALTER TABLE notifications ADD COLUMN user_id BIGINT UNSIGNED NULL AFTER id');
        echo "✅ Added user_id column\n";
    }
    
    if (!Schema::hasColumn('notifications', 'title')) {
        DB::statement('ALTER TABLE notifications ADD COLUMN title VARCHAR(255) NULL AFTER type');
        echo "✅ Added title column\n";
    }
    
    if (!Schema::hasColumn('notifications', 'message')) {
        DB::statement('ALTER TABLE notifications ADD COLUMN message TEXT NULL AFTER title');
        echo "✅ Added message column\n";
    }
    
    if (!Schema::hasColumn('notifications', 'related_url')) {
        DB::statement('ALTER TABLE notifications ADD COLUMN related_url VARCHAR(255) NULL AFTER message');
        echo "✅ Added related_url column\n";
    }
    
    if (!Schema::hasColumn('notifications', 'is_read')) {
        DB::statement('ALTER TABLE notifications ADD COLUMN is_read TINYINT(1) DEFAULT 0 AFTER related_url');
        echo "✅ Added is_read column\n";
    }
    
    // Add index for user_id if it doesn't exist
    $indexes = DB::select("SHOW INDEX FROM notifications WHERE Key_name = 'notifications_user_id_index'");
    if (empty($indexes)) {
        DB::statement('CREATE INDEX notifications_user_id_index ON notifications (user_id)');
        echo "✅ Added index for user_id column\n";
    }
    
    // Add foreign key constraint if it doesn't exist
    $foreignKeys = DB::select("SELECT * FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'user_id' AND REFERENCED_COLUMN_NAME = 'id'");
    if (empty($foreignKeys)) {
        DB::statement('ALTER TABLE notifications ADD CONSTRAINT fk_notifications_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        echo "✅ Added foreign key constraint for user_id\n";
    }
    
    echo "\n✅ Notifications table updated successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error updating notifications table: " . $e->getMessage() . "\n";
    exit(1);
}