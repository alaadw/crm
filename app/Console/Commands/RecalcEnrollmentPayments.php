<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Enrollment;

class RecalcEnrollmentPayments extends Command
{
    protected $signature = 'enrollments:recalc {--only-active : Only recalc active enrollments} {--dry-run : Show changes without saving}';
    protected $description = 'Recalculate paid/due/status for enrollments from payments (JOD-based) with epsilon tolerance';

    public function handle(): int
    {
        $onlyActive = (bool) $this->option('only-active');
        $dryRun = (bool) $this->option('dry-run');

        $query = Enrollment::query();
        if ($onlyActive) {
            $query->where('is_active', true);
        }

        $count = $query->count();
        $this->info("Processing {$count} enrollments...\n");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $updated = 0;
        $query->chunkById(200, function ($chunk) use (&$updated, $dryRun, $bar) {
            foreach ($chunk as $enrollment) {
                $before = [
                    'paid' => (float) $enrollment->paid_amount,
                    'due' => (float) $enrollment->due_amount,
                    'status' => (string) $enrollment->payment_status,
                ];

                // Recalc in-memory first
                $enrollment->recalcFromPayments(!$dryRun);

                $after = [
                    'paid' => (float) $enrollment->paid_amount,
                    'due' => (float) $enrollment->due_amount,
                    'status' => (string) $enrollment->payment_status,
                ];

                if ($before !== $after) {
                    $updated++;
                    if ($dryRun) {
                        $this->line("#{$enrollment->id} {$enrollment->student_id}/{$enrollment->course_class_id} :: paid {$before['paid']} -> {$after['paid']} | due {$before['due']} -> {$after['due']} | status {$before['status']} -> {$after['status']}");
                    }
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("[Dry-run] Would update {$updated} enrollments.");
        } else {
            $this->info("Updated {$updated} enrollments.");
        }

        return 0;
    }
}
