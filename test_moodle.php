<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Test MoodleService
$service = app(\App\Services\MoodleService::class);
$courses = $service->getAllCourses();

echo "Courses Found: " . $courses->count() . "\n";
echo json_encode($courses, JSON_PRETTY_PRINT) . "\n";
?>
