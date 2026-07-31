<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'mysql') {
            return; // Only support MySQL
        }

        $database = $connection->getDatabaseName();
        $tableName = 'messages';

        // Check if already partitioned to avoid errors
        $partitionCount = DB::table('information_schema.PARTITIONS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $tableName)
            ->whereNotNull('PARTITION_NAME')
            ->count();

        if ($partitionCount > 0) {
            return;
        }

        // 1. Drop foreign key if it exists
        $hasForeignKey = collect(DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = 'messages_ticket_id_foreign'", [$tableName]))->isNotEmpty();
        
        if ($hasForeignKey) {
            DB::statement("ALTER TABLE {$tableName} DROP FOREIGN KEY messages_ticket_id_foreign");
            
            // Re-add index since dropping foreign key often removes the index it created
            $hasIndex = collect(DB::select("SHOW INDEXES FROM {$tableName} WHERE Key_name = 'messages_ticket_id_index'"))->isNotEmpty();
            if (!$hasIndex) {
                 DB::statement("CREATE INDEX messages_ticket_id_index ON {$tableName}(ticket_id)");
            }
        }

        // 2. Ensure created_at is NOT NULL before adding it to the primary key
        DB::statement("UPDATE {$tableName} SET created_at = CURRENT_TIMESTAMP WHERE created_at IS NULL");
        DB::statement("ALTER TABLE {$tableName} MODIFY created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");

        // 3. Drop and recreate primary key
        DB::statement("ALTER TABLE {$tableName} DROP PRIMARY KEY, ADD PRIMARY KEY (id, created_at)");

        // 4. Create initial partitions using UNIX_TIMESTAMP since created_at is a TIMESTAMP (timezone dependent)
        $sql = "ALTER TABLE {$tableName} PARTITION BY RANGE (UNIX_TIMESTAMP(created_at)) (\n";
        
        $now = Carbon::now();
        $currentYear = $now->year;
        
        $partitions = [];
        
        // Let's create semi-annual partitions from 3 years ago to 1 year in the future
        for ($year = $currentYear - 3; $year <= $currentYear + 1; $year++) {
            // First half: Jan - Jun (values less than Jul 1)
            $july1stTimestamp = Carbon::create($year, 7, 1)->timestamp;
            $partitions[] = "PARTITION p{$year}_1 VALUES LESS THAN ({$july1stTimestamp})";
            
            // Second half: Jul - Dec (values less than Jan 1 next year)
            $jan1stNextTimestamp = Carbon::create($year + 1, 1, 1)->timestamp;
            $partitions[] = "PARTITION p{$year}_2 VALUES LESS THAN ({$jan1stNextTimestamp})";
        }
        
        $sql .= implode(",\n", $partitions);
        $sql .= "\n)";

        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'mysql') {
            return;
        }

        $database = $connection->getDatabaseName();
        $tableName = 'messages';
        
        $partitionCount = DB::table('information_schema.PARTITIONS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $tableName)
            ->whereNotNull('PARTITION_NAME')
            ->count();

        if ($partitionCount > 0) {
            // Remove partitioning
            DB::statement("ALTER TABLE {$tableName} REMOVE PARTITIONING");
        }

        // Restore primary key to just id
        // This requires removing auto_increment, modifying pk, restoring auto_increment
        // To be safe and simple, we'll just try to restore it if it's the composite one
        try {
            DB::statement("ALTER TABLE {$tableName} MODIFY id BIGINT UNSIGNED NOT NULL"); // remove auto increment temporarily
            DB::statement("ALTER TABLE {$tableName} DROP PRIMARY KEY, ADD PRIMARY KEY (id)");
            DB::statement("ALTER TABLE {$tableName} MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
            
            // Note: We might also want to restore the foreign key, but it's not strictly necessary for rollback
            // unless we want full integrity back.
            DB::statement("ALTER TABLE {$tableName} ADD CONSTRAINT messages_ticket_id_foreign FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE");
        } catch (\Exception $e) {
            // Log or ignore if down fails partially
        }
    }
};
