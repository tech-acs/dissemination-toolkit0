<?php

namespace App\Console\Commands;

use App\Models\Dimension;
use Illuminate\Console\Command;
use App\Actions\RemoveDimensionAction;

// ToDo: Add optional ability to specify table prefix
class RemoveDimension extends Command
{
    protected $signature = 'data:remove-dimension {id}';

    protected $description = 'Drop dimension table, remove all foreign keys from fact tables and delete dimension entry';

    public function handle()
    {
        $dimension = Dimension::find($this->argument('id'));
        $successful = (new RemoveDimensionAction)->handle($dimension);
        if ($successful) {
            $this->info("Dimension {$dimension->name} has been removed.");
        } else {
            $this->error("Error removing {$dimension->name} dimension.");
        }
    }
}
