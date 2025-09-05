<?php

namespace App\Console\Commands;

use App\Models\RecurringTodo;
use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateDailyTodos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'todos:generate-daily {--date= : Specific date to generate todos for (Y-m-d format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily todos from recurring todos for today or specified date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        $dayOfWeek = strtolower($date->format('l'));
        
        $this->info("Generating daily todos for {$date->format('Y-m-d')} ({$dayOfWeek})...");

        $recurringTodos = RecurringTodo::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();

        if ($recurringTodos->isEmpty()) {
            $this->info("No active recurring todos found for {$dayOfWeek}.");
            return;
        }

        $created = 0;
        $skipped = 0;

        foreach ($recurringTodos as $recurringTodo) {
            $existingTodo = Todo::where('user_id', $recurringTodo->user_id)
                ->where('date', $date->format('Y-m-d'))
                ->where('recurring_todo_id', $recurringTodo->id)
                ->first();

            if ($existingTodo) {
                $skipped++;
                continue;
            }

            Todo::create([
                'user_id' => $recurringTodo->user_id,
                'title' => $recurringTodo->title,
                'description' => $recurringTodo->description,
                'date' => $date->format('Y-m-d'),
                'recurring_todo_id' => $recurringTodo->id,
                'is_completed' => false,
            ]);

            $created++;
        }

        $this->info("Daily todo generation complete!");
        $this->info("Created: {$created} todos");
        $this->info("Skipped: {$skipped} todos (already exist)");
    }
}
