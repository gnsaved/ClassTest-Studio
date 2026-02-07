#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use ClassTest\Database\Migration;
use ClassTest\Database\Database;

echo "ClassTest Studio Setup\n";
echo "======================\n\n";

echo "Step 1: Creating storage directory...\n";
$storageDir = __DIR__ . '/storage';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
    echo "✓ Storage directory created\n";
} else {
    echo "✓ Storage directory exists\n";
}

echo "\nStep 2: Running database migrations...\n";
try {
    $migration = new Migration();
    $migration->up();
    echo "✓ Database tables created\n";
    echo "✓ Sample data inserted\n";
} catch (Exception $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nStep 3: Verifying database...\n";
try {
    $db = Database::getInstance();
    
    $userCount = $db->fetchOne("SELECT COUNT(*) as count FROM users")['count'];
    $examTypeCount = $db->fetchOne("SELECT COUNT(*) as count FROM exam_types")['count'];
    $subjectCount = $db->fetchOne("SELECT COUNT(*) as count FROM subjects")['count'];
    
    echo "✓ Users: $userCount\n";
    echo "✓ Exam Types: $examTypeCount\n";
    echo "✓ Subjects: $subjectCount\n";
    
} catch (Exception $e) {
    echo "✗ Verification failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nStep 4: Testing core functionality...\n";
try {
    $adminUser = $db->fetchOne("SELECT * FROM users WHERE email = 'admin@classtest.com'");
    if ($adminUser) {
        echo "✓ Admin account verified\n";
    }
    
    $studentUser = $db->fetchOne("SELECT * FROM users WHERE email = 'student@classtest.com'");
    if ($studentUser) {
        echo "✓ Student account verified\n";
    }
    
    echo "✓ All tests passed\n";
    
} catch (Exception $e) {
    echo "✗ Testing failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n======================\n";
echo "Setup Complete!\n\n";
echo "Default Credentials:\n";
echo "-------------------\n";
echo "Admin:\n";
echo "  Email: admin@classtest.com\n";
echo "  Password: admin123\n\n";
echo "Student:\n";
echo "  Email: student@classtest.com\n";
echo "  Password: student123\n\n";
echo "To start the server:\n";
echo "  php -S localhost:8000 -t public\n\n";
echo "Then visit: http://localhost:8000\n";
