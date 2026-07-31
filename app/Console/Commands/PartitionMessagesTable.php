<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PartitionMessagesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:partition-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure future partitions exist for the messages table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'mysql') {
            $this->error('This command only supports MySQL databases.');
            return;
        }

        $database = $connection->getDatabaseName();
        $tableName = 'messages';

        // Check if already partitioned
        $partitionCount = DB::table('information_schema.PARTITIONS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $tableName)
            ->whereNotNull('PARTITION_NAME')
            ->count();

        if ($partitionCount === 0) {
            $this->error("Table '{$tableName}' is not partitioned yet. Please run the migrations first.");
            return;
        }

        $this->info("Checking for upcoming partitions on '{$tableName}'...");
        $this->ensureFuturePartitions($tableName, $database);
    }

    protected function ensureFuturePartitions($tableName, $database)
    {
        $existingPartitions = DB::table('information_schema.PARTITIONS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $tableName)
            ->whereNotNull('PARTITION_NAME')
            ->pluck('PARTITION_NAME')
            ->toArray();
            
        $now = Carbon::now();
        $currentYear = $now->year;
        
        // Check next 2 years for semi-annual partitions
        for ($year = $currentYear; $year <= $currentYear + 2; $year++) {
            $p1 = "p{$year}_1";
            if (!in_array($p1, $existingPartitions)) {
                $july1stTimestamp = Carbon::create($year, 7, 1)->timestamp;
                $this->info("Adding partition {$p1}...");
                DB::statement("ALTER TABLE {$tableName} ADD PARTITION (PARTITION {$p1} VALUES LESS THAN ({$july1stTimestamp}))");
            }
            
            $p2 = "p{$year}_2";
            if (!in_array($p2, $existingPartitions)) {
                $jan1stNextTimestamp = Carbon::create($year + 1, 1, 1)->timestamp;
                $this->info("Adding partition {$p2}...");
                DB::statement("ALTER TABLE {$tableName} ADD PARTITION (PARTITION {$p2} VALUES LESS THAN ({$jan1stNextTimestamp}))");
            }
        }
        
        $this->info('Future partitions checked and created if needed.');
    }
}
