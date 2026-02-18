<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityLog;
use Carbon\Carbon;

class CleanupActivityLogs extends Command
{
    protected $signature = 'activity:cleanup {--days=90 : Delete logs older than X days}';

    protected $description = 'Delete old activity logs';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = Carbon::now()->subDays($days);

        $count = ActivityLog::where('created_at', '<', $cutoff)->count();
        ActivityLog::where('created_at', '<', $cutoff)->delete();

        $this->info("✅ Deleted {$count} activity logs older than {$days} days.");

        return self::SUCCESS;
    }
}

