<?php
// app/Services/MoodleService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MoodleService
{
    protected $token;
    protected $url;

    public function __construct()
    {
        $this->token = config('services.moodle.token');
        $this->url = config('services.moodle.url');
    }

public function createUser($user)
{
    // Step 1: Search for user by both username and email in one request
    $searchResponse = Http::asForm()->post($this->url, [
        'wstoken' => $this->token,
        'wsfunction' => 'core_user_get_users',
        'moodlewsrestformat' => 'json',
        'criteria[0][key]' => 'username',
        'criteria[0][value]' => trim(strtolower($user['username'] ?? $user['email'] ?? '')),
        'criteria[1][key]' => 'email',
        'criteria[1][value]' => trim(strtolower($user['email'] ?? '')),
    ]);
	
	
    // Check if the request was successful
    if (!$searchResponse->successful()) {
        throw new \Exception("Failed to search Moodle user: " . $searchResponse->body());
    }
    
    $searchResult = $searchResponse->json();
   // echo 'username'.$user['username'].' '.$user['email'];
    //dd($searchResult['users']);
    // Check for API errors in the response
    if (isset($searchResult['exception'])) {
        throw new \Exception("Moodle API Error: " . $searchResult['message']);
    }
  
    // Step 2: If user exists (found by username or email), return the existing user data
    if (!empty($searchResult['users'])) {
        // Find user that matches either username or email
        $foundUser = null;
        $userEmail = trim(strtolower($user['email']));
        $userName = trim(strtolower($user['username']));
        
        foreach ($searchResult['users'] as $existingUser) {
            if (trim(strtolower($existingUser['username'])) === $userName || 
                trim(strtolower($existingUser['email'])) === $userEmail) {
                $foundUser = $existingUser;
                break;
            }
        }
        
        if ($foundUser) {
            return [
                'id' => $foundUser['id'],
                'username' => $foundUser['username'],
                'email' => $foundUser['email'] ?? $user['email'],
                'firstname' => $foundUser['firstname'] ?? $user['firstname'],
                'lastname' => $foundUser['lastname'] ?? $user['lastname'],
                'exists' => true,
                'found_by' => (trim(strtolower($foundUser['username'])) === $userName) ? 'username' : 'email'
            ];
        }
    }
    
    // Step 3: Create a new user if not found
    $createResponse = Http::asForm()->post($this->url, [
        'wstoken' => $this->token,
        'wsfunction' => 'core_user_create_users',
        'moodlewsrestformat' => 'json',
        'users[0][username]' => trim(strtolower($user['username'])),
        'users[0][password]' => $user['password'],
        'users[0][firstname]' => $user['firstname'],
        'users[0][lastname]' => $user['lastname'] ?? 'User',
        'users[0][email]' => $user['email'],
    ]);
   
    if (!$createResponse->successful()) {
        throw new \Exception("Failed to create Moodle user: " . $createResponse->body());
    }
    
    $createResult = $createResponse->json();
    
    // Check for API errors in create response
    if (isset($createResult['exception'])) {
        $details = $createResult['message'] ?? 'Unknown error';
        if (!empty($createResult['debuginfo'])) {
            $details .= ' (' . $createResult['debuginfo'] . ')';
        }
        throw new \Exception("Moodle User Creation Error: " . $details);
    }
    
    if (isset($createResult[0]['id'])) {
        return [
            'id' => $createResult[0]['id'],
            'username' => $createResult[0]['username'],
            'email' => $user['email'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'exists' => false,
            'found_by' => null
        ];
    } else {
        throw new \Exception("Unexpected response format when creating Moodle user: " . json_encode($createResult));
    }
}
	
	

    public function enrolUser($userId, $courseId)
    {
        $response = Http::asForm()->post($this->url, [
            'wstoken' => $this->token,
            'wsfunction' => 'enrol_manual_enrol_users',
            'moodlewsrestformat' => 'json',
            'enrolments' => [
                [
                    'roleid' => 5, // typically 5 = student
                    'userid' => $userId,
                    'courseid' => $courseId,
                ]
            ]
        ]);

        if (!$response->successful()) {
            throw new \Exception("Failed to enrol Moodle user: " . $response->body());
        }

        $result = $response->json();

        if (isset($result['exception'])) {
            throw new \Exception("Moodle Enrollment Error: " . ($result['message'] ?? 'Unknown error'));
        }

        return $result;
    }

    /**
     * Fetch all available courses from Moodle LMS
     */
    public function getAllCourses()
    {
        $cacheKey = 'moodle:courses';

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey, collect());
        }

        try {
            $response = Http::asForm()->post($this->url, [
                'wstoken' => $this->token,
                'wsfunction' => 'core_course_get_courses',
                'moodlewsrestformat' => 'json',
            ]);

            if (!$response->successful()) {
                throw new \Exception("Failed to fetch Moodle courses: " . $response->body());
            }

            $result = $response->json();

            // Check for API errors
            if (isset($result['exception'])) {
                throw new \Exception("Moodle API Error: " . ($result['message'] ?? 'Unknown error'));
            }

            $coursesList = is_array($result) ? $result : [];

            // Filter out the site course (id=1) and return formatted courses
            $courses = collect($coursesList)->filter(function ($course) {
                return isset($course['id']) && $course['id'] !== 1; // Exclude site course
            })->map(function ($course) {
                return [
                    'id' => $course['id'],
                    'fullname' => $course['fullname'] ?? $course['displayname'] ?? '',
                    'shortname' => $course['shortname'] ?? '',
                    'idnumber' => $course['idnumber'] ?? '',
                    'format' => $course['format'] ?? 'topics',
                ];
            })->values();

            Cache::put($cacheKey, $courses, now()->addDay());

            return $courses;
        } catch (\Exception $e) {
            \Log::error('MoodleService::getAllCourses Error: ' . $e->getMessage());
            return collect([]); // Return empty collection on error
        }
    }
}
