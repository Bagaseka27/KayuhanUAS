<?php
// Simple script to run migrations
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

// First, try to refresh
$status = $kernel->call('migrate:reset', ['--force' => true]);
echo "Migrate:reset status: " . $status . "\n";

// Then run fresh migrations
$status = $kernel->call('migrate', []);
echo "Migrate status: " . $status . "\n";
