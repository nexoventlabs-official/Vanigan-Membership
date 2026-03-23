<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client;
use Exception;

class MongoSetupTrial extends Command
{
    protected $signature = 'mongo:setup-trial';
    protected $description = 'Set up MongoDB trial instance with collections and indexes for vanigan_trial database';

    public function handle()
    {
        $this->info('🚀 Starting MongoDB Trial Setup...');
        $this->line('');

        try {
            // Get MongoDB credentials from .env
            $mongoUrl = config('services.mongodb.url') ?: env('MONGO_URL');
            $dbName = env('MONGO_DB_NAME', 'vanigan');  // ← Same variable the app uses

            if (!$mongoUrl) {
                $this->error('❌ MONGO_URL not found in .env file');
                return 1;
            }

            if (!str_contains($mongoUrl, 'trial')) {
                $this->warn('⚠️  WARNING: MONGO_URL does not contain "trial" - ensure you are connecting to TRIAL instance');
            }

            $this->info("📝 Configuration:");
            $this->line("   Database: $dbName");
            $this->line("   Connection: " . $this->maskConnectionString($mongoUrl));
            $this->line('');

            // ==========================================
            // STEP 1: Test Connection
            // ==========================================
            $this->info('📡 STEP 1: Testing Connection...');
            $client = new Client($mongoUrl);

            try {
                $client->selectDatabase($dbName)->command(['ping' => 1]);
                $this->info('✅ Connected to MongoDB successfully');
            } catch (Exception $e) {
                $this->error('❌ Connection Failed: ' . $e->getMessage());
                return 1;
            }

            // ==========================================
            // STEP 2: Select/Create Database
            // ==========================================
            $this->info('');
            $this->info('🗄️  STEP 2: Selecting Database...');
            $db = $client->selectDatabase($dbName);
            $this->info("✅ Using database: $dbName");

            // ==========================================
            // STEP 3: Create Collections
            // ==========================================
            $this->info('');
            $this->info('📚 STEP 3: Creating Collections...');

            $collections = ['members', 'loan_requests', 'manual_entries'];
            $createdCollections = [];

            foreach ($collections as $collectionName) {
                try {
                    // Check if collection exists (convert iterator to array)
                    $existingCollections = iterator_to_array($db->listCollectionNames());

                    if (in_array($collectionName, $existingCollections)) {
                        $this->line("   ⚠️  Collection '{$collectionName}' already exists (skipping creation)");
                    } else {
                        // Create collection
                        $db->createCollection($collectionName);
                        $this->line("   ✅ Created collection: {$collectionName}");
                        $createdCollections[] = $collectionName;
                    }
                } catch (Exception $e) {
                    $this->warn("   ⚠️  Could not create '{$collectionName}': " . $e->getMessage());
                }
            }

            // ==========================================
            // STEP 4: Create Indexes
            // ==========================================
            $this->info('');
            $this->info('🔍 STEP 4: Creating Indexes...');

            // Members collection indexes
            $membersCollection = $db->selectCollection('members');
            $memberIndexes = [
                ['key' => ['mobile' => 1], 'name' => 'idx_mobile'],
                ['key' => ['unique_id' => 1], 'name' => 'idx_unique_id'],
                ['key' => ['epic_no' => 1], 'name' => 'idx_epic_no'],
                ['key' => ['created_at' => -1], 'name' => 'idx_created_at_desc'],
            ];

            $this->line('   📑 Members collection:');
            foreach ($memberIndexes as $indexDef) {
                try {
                    $result = $membersCollection->createIndex(
                        $indexDef['key'],
                        ['name' => $indexDef['name']]
                    );
                    $fieldNames = implode(', ', array_keys($indexDef['key']));
                    $direction = current($indexDef['key']) == 1 ? 'ASC' : 'DESC';
                    $this->line("      ✅ Created index: {$indexDef['name']} ({$fieldNames} {$direction})");
                } catch (Exception $e) {
                    $this->warn("      ⚠️  Could not create index {$indexDef['name']}: " . $e->getMessage());
                }
            }

            // Loan requests collection indexes
            $loanRequestsCollection = $db->selectCollection('loan_requests');
            $this->line('   📑 Loan Requests collection:');
            try {
                $loanRequestsCollection->createIndex(
                    ['unique_id' => 1],
                    ['name' => 'idx_unique_id']
                );
                $this->line('      ✅ Created index: idx_unique_id (unique_id ASC)');
            } catch (Exception $e) {
                $this->warn('      ⚠️  Could not create index: ' . $e->getMessage());
            }

            // ==========================================
            // STEP 5: Verify Setup
            // ==========================================
            $this->info('');
            $this->info('✔️  STEP 5: Verifying Setup...');

            // List all collections
            $allCollections = iterator_to_array($db->listCollectionNames());
            $this->line('   Collections:');
            foreach ($allCollections as $col) {
                $count = $db->selectCollection($col)->countDocuments();
                $this->line("      • {$col} ({$count} documents)");
            }

            // List indexes for members
            $this->line('');
            $this->line('   Indexes (members collection):');
            try {
                $indexes = $membersCollection->listIndexes();
                foreach ($indexes as $index) {
                    $indexName = $index['name'] ?? 'unnamed';
                    $keyFields = json_encode($index['key'] ?? []);
                    $this->line("      • {$indexName}: {$keyFields}");
                }
            } catch (Exception $e) {
                $this->warn('      ⚠️  Could not list indexes: ' . $e->getMessage());
            }

            // ==========================================
            // STEP 6: Final Verification
            // ==========================================
            $this->info('');
            $this->info('✅ FINAL VERIFICATION:');

            $requiredCollections = ['members', 'loan_requests', 'manual_entries'];
            $allPresent = true;

            foreach ($requiredCollections as $col) {
                $exists = in_array($col, $allCollections);
                $status = $exists ? '✅' : '❌';
                $this->line("   $status {$col}");
                if (!$exists) {
                    $allPresent = false;
                }
            }

            $this->line('');

            if ($allPresent) {
                $this->info('🎉 MONGODB TRIAL SETUP COMPLETE!');
                $this->line('');
                $this->info('Summary:');
                $this->line("   ✅ Database: {$dbName}");
                $this->line("   ✅ Collections: " . count($allCollections));
                $this->line("   ✅ Connection: Working");
                $this->line("   ✅ Indexes: Created successfully");
                $this->line('');
                $this->info('Ready for trial testing! 🚀');
                return 0;
            } else {
                $this->error('❌ Some collections are missing. Please check the output above.');
                return 1;
            }

        } catch (Exception $e) {
            $this->line('');
            $this->error('❌ MongoDB Setup Failed');
            $this->error('Error: ' . $e->getMessage());
            $this->line('');
            $this->warn('Troubleshooting:');
            $this->warn('1. Verify MONGO_URL in .env is correct');
            $this->warn('2. Verify MongoDB Atlas connection string is for TRIAL instance');
            $this->warn('3. Verify credentials (username/password) in connection string');
            $this->warn('4. Check that MongoDB PHP extension is installed: php -m | grep mongo');
            return 1;
        }
    }

    /**
     * Mask sensitive parts of MongoDB connection string for display
     */
    private function maskConnectionString(string $url): string
    {
        // Hide password in connection string
        $url = preg_replace(
            '/mongodb\+srv:\/\/[^:]+:([^@]+)@/',
            'mongodb+srv://***:***@',
            $url
        );
        return $url;
    }
}
