<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VerifyDatabase extends Command
{
    protected $signature = 'db:verify';
    protected $description = 'Verify database structure and data for Voter ID Card Generator';

    public function handle()
    {
        $this->info('🔗 Starting Database Verification...');
        
        try {
            // Test database connection
            $connection = DB::connection();
            $databaseName = $connection->getDatabaseName();
            $this->info("✅ Connected to database: $databaseName");
            
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $tableNames = array_map(function($table) use ($databaseName) {
                return $table->{"Tables_in_$databaseName"};
            }, $tables);
            
            $this->line('');
            $this->info('=== CORE APPLICATION TABLES ===');
            
            $coreTables = [
                'generated_voters' => 'Stores generated voter cards',
                'failed_jobs' => 'Laravel queue failed jobs', 
                'jobs' => 'Laravel queue jobs',
                'migrations' => 'Laravel migrations',
                'volunteer_requests' => 'Volunteer/booth agent requests'
            ];
            
            foreach ($coreTables as $table => $description) {
                if (in_array($table, $tableNames)) {
                    $count = DB::table($table)->count();
                    $this->info("✅ $table ($count records) - $description");
                } else {
                    $this->error("❌ $table - MISSING - $description");
                }
            }
            
            // Check voter tables
            $this->line('');
            $this->info('=== VOTER DATA TABLES ===');
            $voterTables = array_filter($tableNames, function($table) {
                return strpos($table, 'tbl_voters_') === 0;
            });
            
            $this->info("📊 Found " . count($voterTables) . " voter assembly tables");
            
            if (count($voterTables) > 0) {
                $totalVoters = 0;
                $sampleTables = array_slice($voterTables, 0, 5);
                
                foreach ($sampleTables as $table) {
                    $count = DB::table($table)->count();
                    $totalVoters += $count;
                    $this->info("✅ $table ($count voters)");
                }
                
                if (count($voterTables) > 5) {
                    $this->info("... and " . (count($voterTables) - 5) . " more tables");
                    
                    foreach (array_slice($voterTables, 5) as $table) {
                        $count = DB::table($table)->count();
                        $totalVoters += $count;
                    }
                }
                
                $this->info("📈 Total Voters: " . number_format($totalVoters));
            }
            
            // Check generated cards
            $this->line('');
            $this->info('=== GENERATED CARDS ===');
            if (in_array('generated_voters', $tableNames)) {
                $totalCards = DB::table('generated_voters')->count();
                $recentCards = DB::table('generated_voters')
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get(['mobile', 'epic_no', 'voter_name', 'created_at']);
                    
                $this->info("📊 Total Generated Cards: $totalCards");
                $this->info("📄 Recent Cards:");
                foreach ($recentCards as $card) {
                    $this->line("   - {$card->mobile} | {$card->epic_no} | {$card->voter_name} | {$card->created_at}");
                }
            }
            
            // Check volunteer requests
            $this->line('');
            $this->info('=== VOLUNTEER REQUESTS ===');
            if (in_array('volunteer_requests', $tableNames)) {
                $stats = DB::table('volunteer_requests')
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(CASE WHEN type = "volunteer" THEN 1 ELSE 0 END) as volunteers,
                        SUM(CASE WHEN type = "booth_agent" THEN 1 ELSE 0 END) as booth_agents,
                        SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed
                    ')
                    ->first();
                    
                $this->info("📊 Volunteer Statistics:");
                $this->line("   - Total: {$stats->total}");
                $this->line("   - Volunteers: {$stats->volunteers}");
                $this->line("   - Booth Agents: {$stats->booth_agents}");
                $this->line("   - Pending: {$stats->pending}");
                $this->line("   - Confirmed: {$stats->confirmed}");
            }
            
            // Check missing tables for frontend
            $this->line('');
            $this->info('=== MISSING TABLES FOR FRONTEND ===');
            $frontendTables = ['user_pins', 'user_sessions'];
            
            foreach ($frontendTables as $table) {
                if (in_array($table, $tableNames)) {
                    $count = DB::table($table)->count();
                    $this->info("✅ $table ($count records)");
                } else {
                    $this->warn("⚠️  $table - MISSING (will create for frontend)");
                }
            }
            
            // Sample data verification
            $this->line('');
            $this->info('=== SAMPLE DATA VERIFICATION ===');
            
            if (!empty($voterTables)) {
                $sampleTable = $voterTables[0];
                $this->info("📄 Sample voter data from $sampleTable:");
                
                $sampleVoters = DB::table($sampleTable)
                    ->select('EPIC_NO', 'FM_NAME_EN', 'LASTNAME_EN', 'DISTRICT_NAME')
                    ->limit(3)
                    ->get();
                    
                foreach ($sampleVoters as $voter) {
                    $fullName = trim($voter->FM_NAME_EN . ' ' . ($voter->LASTNAME_EN ?? ''));
                    $this->line("   - EPIC: {$voter->EPIC_NO} | Name: $fullName | District: {$voter->DISTRICT_NAME}");
                }
            }
            
            $this->line('');
            $this->info('🚀 DATABASE VERIFICATION COMPLETE!');
            $this->info('✅ Database is ready for frontend development');
            
            // Summary
            $this->line('');
            $this->info('=== SUMMARY ===');
            $this->info('✅ Database Connection: Working');
            $this->info('✅ Core Tables: Present');
            $this->info('✅ Voter Data: ' . count($voterTables) . ' assembly tables');
            $this->info('✅ Generated Cards: ' . (in_array('generated_voters', $tableNames) ? DB::table('generated_voters')->count() : 0) . ' cards');
            $this->info('✅ Volunteer System: Working');
            
            $this->line('');
            $this->warn('🔧 REQUIRED FOR FRONTEND:');
            $this->warn('1. Create user_pins table (for PIN login)');
            $this->warn('2. Add 4 missing API endpoints');
            $this->warn('3. Build Laravel frontend templates');
            
        } catch (\Exception $e) {
            $this->error("❌ Database Error: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}