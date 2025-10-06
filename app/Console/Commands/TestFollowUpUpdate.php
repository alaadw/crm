<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FollowUp;
use Carbon\Carbon;

class TestFollowUpUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:follow-up-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test updating a follow-up record';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $followUp = FollowUp::first();
        
        if (!$followUp) {
            $this->error('No follow-ups found');
            return 1;
        }
        
        $this->info('Testing follow-up update...');
        $this->info('Follow-up ID: ' . $followUp->id);
        $this->info('Current purpose: ' . $followUp->purpose);
        $this->info('Current priority: ' . $followUp->priority);
        
        // Try to update the purpose
        $newPurpose = 'Updated purpose - ' . now();
        $newPriority = $followUp->priority === 'high' ? 'medium' : 'high';
        
        $result = $followUp->update([
            'purpose' => $newPurpose,
            'priority' => $newPriority
        ]);
        
        $this->info('Update result: ' . ($result ? 'Success' : 'Failed'));
        
        // Check if the update was saved
        $freshFollowUp = $followUp->fresh();
        $this->info('New purpose: ' . $freshFollowUp->purpose);
        $this->info('New priority: ' . $freshFollowUp->priority);
        
        if ($freshFollowUp->purpose === $newPurpose && $freshFollowUp->priority === $newPriority) {
            $this->info('✅ Follow-up update test PASSED');
            return 0;
        } else {
            $this->error('❌ Follow-up update test FAILED');
            return 1;
        }
    }
}
