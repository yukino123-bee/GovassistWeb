<?php

namespace App\Console\Commands;

use App\Models\UserInquiry;
use Illuminate\Console\Command;

class PruneInactiveInquiries extends Command
{
    protected $signature = 'inquiries:prune-inactive';

    protected $description = 'Delete conversations whose resident has not answered a staff reply within the timeout, or that have been inactive for a day';

    public function handle(): int
    {
        $deleted = 0;

        UserInquiry::query()->stale()->each(function (UserInquiry $inquiry) use (&$deleted): void {
            $inquiry->responses()->delete();
            $inquiry->delete();
            $deleted++;
        });

        $this->info("Deleted {$deleted} inactive conversation(s).");

        return self::SUCCESS;
    }
}
