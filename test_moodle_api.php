<?php
// Quick test script to check Moodle API connectivity

$token = 'd90ace7c58f05a1f53c509b6fd121bb9';
$baseUrl = 'https://lms.thehope-tech.com/webservice/rest/server.php';

echo "Testing Moodle API Connection...\n";
echo "URL: $baseUrl\n";
echo "Token: " . substr($token, 0, 10) . "...\n\n";

// Test 1: Get site info (basic permission check)
echo "Test 1: Getting site info...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl?wstoken=$token&wsfunction=core_webservice_get_site_info&moodlewsrestformat=json");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$siteInfo = json_decode($response, true);
echo "HTTP Code: $httpCode\n";
echo "Response: " . json_encode($siteInfo, JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Get courses
echo "Test 2: Getting courses...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl?wstoken=$token&wsfunction=core_course_get_courses&moodlewsrestformat=json");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$courses = json_decode($response, true);
echo "HTTP Code: $httpCode\n";
echo "Courses Count: " . (isset($courses[0]) ? count($courses) : 0) . "\n";

if (!empty($courses)) {
    echo "First 3 Courses:\n";
    for ($i = 0; $i < min(3, count($courses)); $i++) {
        echo "  - ID: " . $courses[$i]['id'] . ", Name: " . $courses[$i]['fullname'] . "\n";
    }
} else {
    echo "Full Response: " . json_encode($courses, JSON_PRETTY_PRINT) . "\n";
}

echo "\n✅ Test complete. Check response above for any error messages.\n";
?>
