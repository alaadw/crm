<?php
// Quick test to verify Laravel MoodleService is working

require __DIR__ . '/bootstrap/app.php';

$app = new \Illuminate\Foundation\Application(
    dirname(__DIR__)
);

$app->bind(
    \Illuminate\Contracts\Http\Kernel::class,
    \App\Http\Kernel::class,
);

$app->bind(
    \Illuminate\Contracts\Console\Kernel::class,
    \App\Console\Kernel::class,
);

$app->bind(
    \Illuminate\Contracts\Debug\ExceptionHandler::class,
    \App\Exceptions\Handler::class,
);

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Now test the service
$moodleService = app('App\Services\MoodleService');
$courses = $moodleService->getAllCourses();

echo "✅ Laravel MoodleService Test\n";
echo "Total Courses: " . count($courses) . "\n";
echo "First 5 Courses:\n";

foreach ($courses->take(5) as $course) {
    echo "  - ID: {$course['id']}, Name: {$course['fullname']}\n";
}

echo "\nJSON Response Format:\n";
echo json_encode([
    'success' => true,
    'data' => $courses->take(3)->toArray()
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
