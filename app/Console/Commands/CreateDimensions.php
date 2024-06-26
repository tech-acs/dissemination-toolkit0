<?php

namespace App\Console\Commands;

use App\Models\Dimension;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ToDo: Add optional ability to specify table prefix
class CreateDimensions extends Command
{
    protected $signature = 'data:create-dimensions {id?}';

    protected $description = 'Create dimension tables (only if they do not already exist) for each of the entries in the dimensions table';

    private function createTable(string $table, array $columns): bool
    {
        if (Schema::hasTable($table)) {
            $this->warn("Dimension table '{$table}' already exists.");
            /*if ($this->option('drop')) {
                $this->warn("Dropping...");
                Schema::drop($table);
                // Also the foreign key?
            }*/
        } else {
            $this->newLine()->info("Attempting to create $table dimension table...");
            try {
                Schema::create($table, function (Blueprint $table) use ($columns) {
                    $table->id();
                    foreach ($columns as $column => $columnDetails) {
                        $table->addColumn($columnDetails['type'] ?? 'string', $column, $columnDetails['parameters'] ?? []);
                    }
                });
                return true;
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
                return false;
            }
        }
        return false;
    }

    public function handle()
    {
        $dimensionId = $this->argument('id');
        if (is_null($dimensionId)) {
            $dimensions = Dimension::all();
        } else {
            $dimensions = Dimension::whereId($dimensionId)->get();
        }
        $columns = [
            'code' => [
                'type' => 'string',
                'parameters' => ['length' => 25],
            ],
            'name' => [
                'type' => 'string',
            ]
        ];
        foreach ($dimensions as $dimension) {
            $tableName = $dimension->table_name;
            $isCreated = $this->createTable($tableName, $columns);
            if ($isCreated) {
                $dimension->update(['table_created_at' => now()]);
                foreach ($dimension->for as $factTable) {
                    try {
                        Schema::table($factTable, function (Blueprint $table) use ($tableName) {
                            $table->foreignId($tableName . '_id')->nullable();
                        });
                    } catch (\Exception $exception) {
                        $this->error($exception->getMessage());
                    }
                }
            }
        }
    }
}
