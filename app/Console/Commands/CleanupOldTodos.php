<?php

namespace App\Console\Commands;

use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupOldTodos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'todos:cleanup-old {--days=7 : Number of days to keep todos for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old todos older than specified number of days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::today()->subDays($days);
        
        $this->info("Cleaning up todos older than {$days} days (before {$cutoffDate->format('Y-m-d')})...");

        $count = Todo::where('date', '<', $cutoffDate->format('Y-m-d'))->count();
        
        if ($count === 0) {
            $this->info("No old todos found to cleanup.");
            return;
        }

        if ($this->confirm("This will delete {$count} todos. Continue?")) {
            $deleted = Todo::where('date', '<', $cutoffDate->format('Y-m-d'))->delete();
            $this->info("Deleted {$deleted} old todos.");
        } else {
            $this->info("Cleanup cancelled.");
        }
    }
}
