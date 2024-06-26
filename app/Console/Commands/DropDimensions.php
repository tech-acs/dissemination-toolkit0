<?php

namespace App\Console\Commands;

use App\Models\Dimension;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ToDo: Add optional ability to specify table prefix
class DropDimensions extends Command
{
    protected $signature = 'data:drop-dimensions';

    protected $description = 'Drop dimension tables for each of the entries in the dimensions table';

    private function dropTable(string $table): bool
    {
        if (Schema::hasTable($table)) {
            $this->newLine()->info("Attempting to drop $table dimension table...");
            try {
                Schema::drop($table);
                return true;
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
                return false;
            }
        } else {
            $this->error("$table dimension table does not exist");
        }
        return false;
    }

    public function handle()
    {
        $dimensions = Dimension::all();
        foreach ($dimensions as $dimension) {
            $tableName = $dimension->table_name;
            $isDropped = $this->dropTable($tableName);
            if ($isDropped) {
                $this->info("... table dropped!");
                foreach ($dimension->for as $factTable) {
                    try {
                        Schema::table($factTable, function (Blueprint $table) use ($tableName) {
                            $table->dropColumn($tableName . '_id');
                        });
                        $this->info("... respective foreign key dropped from $factTable");
                    } catch (\Exception $exception) {
                        $this->error($exception->getMessage());
                    }
                }
            }
        }
    }
}
