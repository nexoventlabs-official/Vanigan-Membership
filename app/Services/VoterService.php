<?php

namespace App\Services;

use App\Models\AssemblyConstituency;
use Illuminate\Support\Facades\DB;

class VoterService
{
    protected $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Initialization helper for static methods to get CacheService instance
     */
    private static function getCacheService(): CacheService
    {
        return app(CacheService::class);
    }
    /**
     * Find a voter by EPIC number across all assembly tables
     * Results are cached for 10 minutes since voter data is read-only
     * 
     * @param string $epicNo
     * @return array|null
     */
    public static function findByEpicNo(string $epicNo): ?array
    {
        $cacheKey = "voter:epic:{$epicNo}";

        return self::getCacheService()->remember($cacheKey, 600, function () use ($epicNo) {
            $tables = AssemblyConstituency::getAllVoterTables();
            
            // Search across all assembly tables using UNION
            // Process in batches of 10 tables for better performance
            $batches = array_chunk($tables, 10);
            
            foreach ($batches as $batch) {
                $unions = [];
                $bindings = [];
                
                foreach ($batch as $table) {
                    $unions[] = "(SELECT * FROM `{$table}` WHERE `EPIC_NO` = ? LIMIT 1)";
                    $bindings[] = $epicNo;
                }
                
                $sql = implode(' UNION ALL ', $unions) . ' LIMIT 1';
                $result = DB::connection('voters')->select($sql, $bindings);
                
                if (!empty($result)) {
                    return (array) $result[0];
                }
            }
            
            return null;
        });
    }

    /**
     * Search voters by name across specified assembly tables
     * 
     * @param string $searchTerm
     * @param string|null $assembly
     * @param string|null $district
     * @param int $limit
     * @return array
     */
    public static function searchByName(
        string $searchTerm,
        ?string $assembly = null,
        ?string $district = null,
        int $limit = 50
    ): array {
        $tables = self::getVoterTables($assembly, $district);
        
        if (empty($tables)) {
            return [];
        }
        
        $results = [];
        $searchTerm = '%' . $searchTerm . '%';
        
        foreach ($tables as $table) {
            $query = DB::connection('voters')
                ->table($table)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('NAME', 'LIKE', $searchTerm)
                      ->orWhere('FM_NAME_EN', 'LIKE', $searchTerm)
                      ->orWhere('LASTNAME_EN', 'LIKE', $searchTerm);
                })
                ->limit($limit);
            
            $tableResults = $query->get()->toArray();
            $results = array_merge($results, $tableResults);
            
            if (count($results) >= $limit) {
                break;
            }
        }
        
        return array_slice($results, 0, $limit);
    }

    /**
     * Get voter tables filtered by assembly or district
     * 
     * @param string|null $assembly
     * @param string|null $district
     * @return array
     */
    protected static function getVoterTables(?string $assembly = null, ?string $district = null): array
    {
        if ($assembly) {
            $table = AssemblyConstituency::getTableByAssembly($assembly);
            return $table ? [$table] : [];
        }
        
        if ($district) {
            return AssemblyConstituency::getTablesByDistrict($district);
        }
        
        return AssemblyConstituency::getAllVoterTables();
    }

    /**
     * Count voters matching criteria
     * 
     * @param string|null $assembly
     * @param string|null $district
     * @param array $where Additional WHERE conditions
     * @return int
     */
    public static function countVoters(
        ?string $assembly = null,
        ?string $district = null,
        array $where = []
    ): int {
        // If no WHERE conditions, use pre-calculated totals from assembly table
        if (empty($where)) {
            if ($assembly) {
                $assemblyRecord = AssemblyConstituency::where('assembly_name', 'ilike', $assembly)->first();
                return $assemblyRecord?->total_voters ?? 0;
            }
            
            if ($district) {
                return AssemblyConstituency::where('district_name', 'ilike', $district)
                    ->sum('total_voters');
            }
            
            return AssemblyConstituency::getTotalVotersCount();
        }
        
        // Otherwise, count across tables
        $tables = self::getVoterTables($assembly, $district);
        $total = 0;
        
        foreach ($tables as $table) {
            $query = DB::connection('voters')->table($table);
            
            foreach ($where as $column => $value) {
                $query->where($column, $value);
            }
            
            $total += $query->count();
        }
        
        return $total;
    }

    /**
     * Get distinct values for a column across assembly tables
     * 
     * @param string $column
     * @param string|null $assembly
     * @param string|null $district
     * @return array
     */
    public static function getDistinctValues(
        string $column,
        ?string $assembly = null,
        ?string $district = null
    ): array {
        // Optimized for assembly and district columns
        if ($column === 'ASSEMBLY_NAME') {
            return AssemblyConstituency::getDistinctAssemblyNames();
        }
        
        if ($column === 'DISTRICT_NAME') {
            return AssemblyConstituency::getDistinctDistrictNames();
        }
        
        // For other columns, scan tables
        $tables = self::getVoterTables($assembly, $district);
        $values = [];
        
        foreach ($tables as $table) {
            $results = DB::connection('voters')
                ->table($table)
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->distinct()
                ->pluck($column)
                ->toArray();
            
            $values = array_merge($values, $results);
        }
        
        return array_values(array_unique($values));
    }

    /**
     * Clear voter cache
     * 
     * @param string|null $epicNo If provided, clears only that voter's cache
     * @return void
     */
    public static function clearCache(?string $epicNo = null): void
    {
        if ($epicNo) {
            self::getCacheService()->forget("voter:epic:{$epicNo}");
        } else {
            self::getCacheService()->forget('assembly_tables');
            // Note: Individual voter caches will expire naturally after 10 minutes
        }
    }
}
