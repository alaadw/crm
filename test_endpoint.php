<?php
// Test the classes.moodle-courses route directly
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Create a request
$request = \Illuminate\Http\Request::create('/classes/moodle-courses', 'GET');
$request = $request->createFromBase($request);

try {
    $response = $kernel->handle($request);
    echo $response->getContent();
} catch (\Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTrace()
    ], JSON_PRETTY_PRINT);
}
?>
