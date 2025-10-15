<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Enrollment;

class DebugStudents extends Command
{
    protected $signature = 'debug:students';
    protected $description = 'Debug student data';

    public function handle()
    {
        $this->info('=== Students ===');
        $students = Student::take(5)->get(['id', 'full_name', 'email']);
        $this->line("Total students: " . Student::count());
        
        foreach ($students as $student) {
            $this->line("Student #{$student->id}: {$student->name} ({$student->email})");
        }
        
        $this->info('\n=== Enrollments with Student IDs ===');
        $enrollments = Enrollment::take(5)->get(['id', 'student_id']);
        
        foreach ($enrollments as $enrollment) {
            $this->line("Enrollment #{$enrollment->id}: student_id = {$enrollment->student_id}");
            
            // Try to load student manually
            $student = Student::find($enrollment->student_id);
            if ($student) {
                $this->line("  Found student: {$student->name}");
            } else {
                $this->line("  Student not found!");
            }
        }
        
        $this->info('\n=== Test Enrollment->Student Relationship ===');
        $enrollment = Enrollment::with('student')->first();
        if ($enrollment) {
            $this->line("Enrollment #{$enrollment->id}:");
            $this->line("  student_id: {$enrollment->student_id}");
            $this->line("  student object: " . ($enrollment->student ? $enrollment->student->name : 'NULL'));
        }
        
        return 0;
    }
}