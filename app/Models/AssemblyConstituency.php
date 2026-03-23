<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssemblyConstituency extends Model
{
    protected $connection = 'voters'; // Use voters database connection
    protected $table = 'tbl_assembly_consitituency';

    protected $fillable = [
        'table_name',
        'assembly_name',
        'assembly_no',
        'district_name',
        'total_voters',
    ];

    protected $casts = [
        'total_voters' => 'integer',
    ];

    /**
     * Get all assembly tables with caching (10 minutes)
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function getAllAssemblies()
    {
        return app(\App\Services\CacheService::class)->remember('assembly_tables', 600, function () {
            return self::orderBy('assembly_no')->get();
        });
    }

    /**
     * Get table name by assembly name
     * 
     * @param string $assemblyName
     * @return string|null
     */
    public static function getTableByAssembly(string $assemblyName): ?string
    {
        $assemblies = self::getAllAssemblies();
        $assembly = $assemblies->firstWhere('assembly_name', 'ilike', $assemblyName);
        return $assembly?->table_name;
    }

    /**
     * Get all table names for a district
     * 
     * @param string $districtName
     * @return array
     */
    public static function getTablesByDistrict(string $districtName): array
    {
        $assemblies = self::getAllAssemblies();
        return $assemblies
            ->filter(fn($a) => strcasecmp($a->district_name, $districtName) === 0)
            ->pluck('table_name')
            ->toArray();
    }

    /**
     * Get all voter table names
     * 
     * @return array
     */
    public static function getAllVoterTables(): array
    {
        return self::getAllAssemblies()->pluck('table_name')->toArray();
    }

    /**
     * Get total voters count across all assemblies
     * 
     * @return int
     */
    public static function getTotalVotersCount(): int
    {
        return self::sum('total_voters');
    }

    /**
     * Get distinct assembly names
     * 
     * @return array
     */
    public static function getDistinctAssemblyNames(): array
    {
        return self::whereNotNull('assembly_name')
            ->where('assembly_name', '!=', '')
            ->distinct()
            ->orderBy('assembly_name')
            ->pluck('assembly_name')
            ->toArray();
    }

    /**
     * Get distinct district names
     * 
     * @return array
     */
    public static function getDistinctDistrictNames(): array
    {
        return self::whereNotNull('district_name')
            ->where('district_name', '!=', '')
            ->distinct()
            ->orderBy('district_name')
            ->pluck('district_name')
            ->toArray();
    }
}
