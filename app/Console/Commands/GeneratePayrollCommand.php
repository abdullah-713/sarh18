<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Console\Command;

class GeneratePayrollCommand extends Command
{
    protected $signature = 'sarh:payroll {--period= : Month period (Y-m), defaults to previous month} {--branch= : Specific branch ID}';

    protected $description = 'Generate payroll records for all employees based on attendance data';

    public function handle(): int
    {
        $period = $this->option('period') ?? now()->subMonth()->format('Y-m');
        $branchId = $this->option('branch');

        $this->info("💰 Generating payroll for period: {$period}...");
        $this->newLine();

        $query = User::where('status', 'active');
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $users = $query->get();
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $success = 0;
        $errors  = 0;

        foreach ($users as $user) {
            try {
                Payroll::generateForUser($user, $period);
                $success++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("Error for {$user->name_ar} (#{$user->id}): {$e->getMessage()}");
                $errors++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Done: {$success} payrolls generated, {$errors} errors");

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
