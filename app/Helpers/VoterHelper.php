<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class VoterHelper
{
    /**
     * Find voter by EPIC number across all assembly tables (ac001, ac002, etc.)
     * Returns voter data from voters DB with caching
     */
    public static function findByEpicNo($epicNo)
    {
        try {
            $epicNo = strtoupper(trim($epicNo));
            
            if (empty($epicNo)) {
                return null;
            }

            // Check cache first (10 minutes for found, 2 minutes for not found)
            $cacheKey = "voter:epic:{$epicNo}";
            $cached = app(\App\Services\CacheService::class)->get($cacheKey);
            
            if ($cached !== null) {
                return isset($cached['epic_no']) && !empty($cached['epic_no']) ? $cached : null;
            }

            // Get all assembly table names
            $tables = self::getAssemblyTableNames();
            
            if (empty($tables)) {
                Log::warning('No assembly tables found');
                return null;
            }

            // Search across assembly tables in batches
            $batchSize = 30;
            for ($i = 0; $i < count($tables); $i += $batchSize) {
                $batch = array_slice($tables, $i, $batchSize);
                
                // Build UNION query for this batch
                $unions = [];
                $bindings = [];
                
                foreach ($batch as $table) {
                    $unions[] = "(SELECT *, '{$table}' as source_table FROM `{$table}` WHERE `EPIC_NO` = ? LIMIT 1)";
                    $bindings[] = $epicNo;
                }
                
                $sql = implode(' UNION ALL ', $unions) . ' LIMIT 1';
                
                try {
                    $row = DB::connection('voters')->selectOne($sql, $bindings);
                    
                    if ($row) {
                        $result = self::translateVoterRow((array) $row);

                        // Cache for 10 minutes (positive hit)
                        app(\App\Services\CacheService::class)->put($cacheKey, $result, 600);
                        
                        Log::info("Voter found: {$epicNo} in table {$row->source_table}");
                        return $result;
                    }
                } catch (Exception $e) {
                    Log::warning("Batch query failed for tables " . implode(',', $batch) . ": " . $e->getMessage());
                    continue;
                }
            }

            // Not found - cache negative result for 2 minutes
            app(\App\Services\CacheService::class)->put($cacheKey, ['epic_no' => ''], 120);
            
            Log::info("Voter not found: {$epicNo}");
            return null;

        } catch (Exception $e) {
            Log::error("VoterHelper::findByEpicNo Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Search voters by name across all assembly tables
     */
    public static function searchByName($searchTerm, $assemblyNo = null, $limit = 20)
    {
        try {
            $searchTerm = trim($searchTerm);
            
            if (strlen($searchTerm) < 2) {
                return [];
            }

            $tables = self::getAssemblyTableNames();
            
            if (empty($tables)) {
                return [];
            }

            // Filter tables by assembly if specified
            if ($assemblyNo) {
                $tables = array_filter($tables, function($table) use ($assemblyNo) {
                    // Extract assembly number from table name (e.g., ac001 -> 1)
                    $tableAssembly = (int) substr($table, 2);
                    return $tableAssembly == $assemblyNo;
                });
            }

            $results = [];
            $batchSize = 20;

            for ($i = 0; $i < count($tables) && count($results) < $limit; $i += $batchSize) {
                $batch = array_slice($tables, $i, $batchSize);
                
                $unions = [];
                $bindings = [];
                
                foreach ($batch as $table) {
                    $unions[] = "(SELECT * FROM `{$table}` WHERE FM_NAME_EN LIKE ? OR LASTNAME_EN LIKE ? LIMIT 5)";
                    $bindings[] = "%{$searchTerm}%";
                    $bindings[] = "%{$searchTerm}%";
                }
                
                $sql = implode(' UNION ALL ', $unions) . ' LIMIT ' . ($limit - count($results));
                
                try {
                    $rows = DB::connection('voters')->select($sql, $bindings);
                    
                    foreach ($rows as $row) {
                        if (count($results) >= $limit) break;
                        $results[] = self::translateVoterRow((array) $row);
                    }
                } catch (Exception $e) {
                    Log::warning("Search batch failed: " . $e->getMessage());
                    continue;
                }
            }

            return $results;

        } catch (Exception $e) {
            Log::error("VoterHelper::searchByName Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count total voters across all assembly tables
     */
    public static function countVoters($assemblyNo = null)
    {
        try {
            $tables = self::getAssemblyTableNames();
            
            if (empty($tables)) {
                return 0;
            }

            // Filter tables by assembly if specified
            if ($assemblyNo) {
                $tables = array_filter($tables, function($table) use ($assemblyNo) {
                    $tableAssembly = (int) substr($table, 2);
                    return $tableAssembly == $assemblyNo;
                });
            }

            $totalCount = 0;
            foreach ($tables as $table) {
                try {
                    $count = DB::connection('voters')->table($table)->count();
                    $totalCount += $count;
                } catch (Exception $e) {
                    Log::warning("Could not count voters in table {$table}: " . $e->getMessage());
                }
            }

            return $totalCount;

        } catch (Exception $e) {
            Log::warning("VoterHelper::countVoters Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all assembly table names (tbl_voters_[name]_[number])
     * Cached for 1 hour
     */
    public static function getAssemblyTableNames()
    {
        try {
            return app(\App\Services\CacheService::class)->remember('voter:assembly_tables', 3600, function () {
                // Get all tables that match voter pattern (tbl_voters_*)
                $tables = DB::connection('voters')->select('SHOW TABLES');
                $voterTables = [];
                
                foreach ($tables as $table) {
                    $tableName = array_values((array)$table)[0];
                    // Match tables like tbl_voters_anna_nagar_21, tbl_voters_ambattur_8, etc.
                    if (preg_match('/^tbl_voters_.*_\d+$/i', $tableName)) {
                        $voterTables[] = $tableName;
                    }
                }
                
                // Sort by assembly number (extract number from end)
                usort($voterTables, function($a, $b) {
                    $numA = (int) preg_replace('/.*_(\d+)$/', '$1', $a);
                    $numB = (int) preg_replace('/.*_(\d+)$/', '$1', $b);
                    return $numA - $numB;
                });

                Log::info("Found " . count($voterTables) . " voter tables");
                return $voterTables;
            });

        } catch (Exception $e) {
            Log::error("VoterHelper::getAssemblyTableNames Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all voter table names (alias for getAssemblyTableNames)
     */
    public static function getVoterTableNames()
    {
        return self::getAssemblyTableNames();
    }

    /**
     * Translate raw voter row to standardized format
     */
    protected static function translateVoterRow($row)
    {
        $epicNo = $row['EPIC_NO'] ?? '';
        $ptcCode = self::generatePtcCode(); // UUID-based, not dependent on EPIC
        
        // Clean name construction - handle empty/hyphen last names
        $firstName = trim($row['FM_NAME_EN'] ?? '');
        $lastName = trim($row['LASTNAME_EN'] ?? '');
        
        // Remove hyphens and clean up last name
        $lastName = str_replace(['-', '_'], '', $lastName);
        $lastName = trim($lastName);
        
        // Build full name - only add last name if it's not empty
        $fullName = $firstName;
        if (!empty($lastName)) {
            $fullName .= ' ' . $lastName;
        }
        $fullName = trim($fullName);
        
        // Clean relation name the same way
        $relationFirstName = trim($row['RLN_FM_NM_EN'] ?? '');
        $relationLastName = trim($row['RLN_L_NM_EN'] ?? '');
        $relationLastName = str_replace(['-', '_'], '', $relationLastName);
        $relationLastName = trim($relationLastName);
        
        $relationName = $relationFirstName;
        if (!empty($relationLastName)) {
            $relationName .= ' ' . $relationLastName;
        }
        $relationName = trim($relationName);
        
        return [
            'epic_no' => $epicNo,
            'name' => $fullName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'assembly' => (string) ($row['AC_NO'] ?? ''),
            'assembly_name' => $row['ASSEMBLY_NAME'] ?? '',
            'age' => $row['AGE'] ?? '',
            'sex' => $row['GENDER'] ?? '',
            'relation_type' => $row['RLN_TYPE'] ?? '',
            'relation_name' => $relationName,
            'mobile' => $row['MOBILE_NO'] ?? '',
            'part_no' => (string) ($row['PART_NO'] ?? ''),
            'section_no' => (string) ($row['SECTION_NO'] ?? ''),
            'house_no' => $row['C_HOUSE_NO'] ?? '',
            'dob' => $row['DOB'] ?? '',
            'district' => $row['DISTRICT_NAME'] ?? '', // Fixed: use DISTRICT_NAME
            'voter_name' => $fullName, // Clean name for CardGenerationService
            'ptc_code' => $ptcCode, // Generate PTC code like Python implementation
            'verified' => true,
        ];
    }

    /**
     * Generate PTC code like Python Flask implementation
     * Matches: def generate_ptc_code() -> str: return f'PTC-{uuid.uuid4().hex[:7].upper()}'
     */
    protected static function generatePtcCode($epicNo = null)
    {
        // Generate UUID-based PTC code (matches Python Flask app.py)
        $uuid = bin2hex(random_bytes(4)); // Generate 8 hex chars, take first 7
        return "PTC-" . strtoupper(substr($uuid, 0, 7));
    }

    /**
     * Generate referral ID like Python Flask implementation
     * Matches: rid = 'REF-' + uuid.uuid4().hex[:8].upper()
     */
    public static function generateReferralId()
    {
        $uuid = bin2hex(random_bytes(4)); // Generate 8 hex chars
        return "REF-" . strtoupper($uuid);
    }

    /**
     * Get or create referral link for a PTC code
     * Matches Python: def get_or_create_referral(ptc_code: str) -> dict | None
     */
    public static function getOrCreateReferral($ptcCode)
    {
        try {
            // Check if referral already exists
            $existing = DB::table('generated_voters')
                ->where('ptc_code', $ptcCode)
                ->whereNotNull('referral_id')
                ->first(['referral_id', 'referral_link']);

            if ($existing) {
                return [
                    'referral_id' => $existing->referral_id,
                    'referral_link' => $existing->referral_link
                ];
            }

            // Generate new referral
            $referralId = self::generateReferralId();
            $referralLink = config('app.url') . "/refer/{$ptcCode}/{$referralId}";

            // Update the record
            DB::table('generated_voters')
                ->where('ptc_code', $ptcCode)
                ->update([
                    'referral_id' => $referralId,
                    'referral_link' => $referralLink
                ]);

            return [
                'referral_id' => $referralId,
                'referral_link' => $referralLink
            ];

        } catch (Exception $e) {
            Log::error("VoterHelper::getOrCreateReferral Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Clear voter cache
     */
    public static function clearCache($epicNo = null)
    {
        try {
            if ($epicNo) {
                app(\App\Services\CacheService::class)->forget("voter:epic:{$epicNo}");
                Log::info("Cleared cache for EPIC: {$epicNo}");
            } else {
                app(\App\Services\CacheService::class)->forget('voter:assembly_tables');
                app(\App\Services\CacheService::class)->forget('voter:assemblies');
                app(\App\Services\CacheService::class)->forget('voter:districts');
                Log::info("Cleared all voter cache");
            }
        } catch (Exception $e) {
            Log::error("VoterHelper::clearCache Error: " . $e->getMessage());
        }
    }

    /**
     * Get distinct assembly names from all tables
     */
    public static function getAssemblies()
    {
        try {
            return app(\App\Services\CacheService::class)->remember('voter:assemblies', 3600, function () {
                $tables = self::getAssemblyTableNames();
                $assemblies = [];
                
                foreach ($tables as $table) {
                    try {
                        $assembly = DB::connection('voters')
                            ->table($table)
                            ->select('AC_NO as assembly_no', 'ASSEMBLY_NAME as assembly_name')
                            ->selectRaw('COUNT(*) as total_voters')
                            ->whereNotNull('AC_NO')
                            ->whereNotNull('ASSEMBLY_NAME')
                            ->groupBy('AC_NO', 'ASSEMBLY_NAME')
                            ->first();
                            
                        if ($assembly) {
                            $assemblies[] = (array) $assembly;
                        }
                    } catch (Exception $e) {
                        Log::warning("Could not get assembly info from {$table}: " . $e->getMessage());
                    }
                }
                
                // Sort by assembly number
                usort($assemblies, function($a, $b) {
                    return $a['assembly_no'] - $b['assembly_no'];
                });
                
                return $assemblies;
            });
        } catch (Exception $e) {
            Log::error("VoterHelper::getAssemblies Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get distinct districts from all tables
     */
    public static function getDistricts()
    {
        try {
            return app(\App\Services\CacheService::class)->remember('voter:districts', 3600, function () {
                $tables = self::getAssemblyTableNames();
                $districts = [];
                
                foreach (array_slice($tables, 0, 10) as $table) { // Sample from first 10 tables
                    try {
                        $tableDistricts = DB::connection('voters')
                            ->table($table)
                            ->distinct()
                            ->whereNotNull('DISTRICT')
                            ->pluck('DISTRICT')
                            ->toArray();
                            
                        $districts = array_merge($districts, $tableDistricts);
                    } catch (Exception $e) {
                        Log::warning("Could not get districts from {$table}: " . $e->getMessage());
                    }
                }
                
                return array_unique($districts);
            });
        } catch (Exception $e) {
            Log::error("VoterHelper::getDistricts Error: " . $e->getMessage());
            return [];
        }
    }
}