<?php

namespace Ameax\SequenceNumber\Commands;

use Illuminate\Console\Command;

class SequenceNumberCommand extends Command
{
    public $signature = 'sequence-number';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
