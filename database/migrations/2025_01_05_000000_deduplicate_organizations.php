<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function isBetterOrganization($existing, $new)
    {
        $score = 0;
        // Prefer records with more complete information
        $score += ! empty($new->address) ? 1 : 0;
        $score += ! empty($new->city) ? 1 : 0;
        $score += ! empty($new->country) ? 1 : 0;
        $score += ! empty($new->website) ? 1 : 0;
        $score += ! empty($new->phone) ? 1 : 0;

        return $score > 0;
    }

    public function up()
    {
        // Find duplicates based on slug
        $duplicates = DB::table('organizations')
            ->select('slug', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
            ->groupBy('slug')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->ids);

            // Find the best record to keep
            $organizations = DB::table('organizations')
                ->whereIn('id', $ids)
                ->get();

            $keepId = $ids[0]; // Default to first ID
            $bestOrg = $organizations->first();

            // Find the most complete record
            foreach ($organizations as $org) {
                if ($this->isBetterOrganization($bestOrg, $org)) {
                    $keepId = $org->id;
                    $bestOrg = $org;
                }
            }

            // Get IDs to remove
            $removeIds = array_diff($ids, [$keepId]);

            // Update foreign keys in all related tables
            $tablesToUpdate = [
                'internship_agreements' => 'organization_id',
                'internship_agreement_contacts' => 'organization_id',
                'projects' => 'organization_id',
                'final_year_projects' => 'organization_id',
                'apprenticeship_projects' => 'organization_id',
            ];

            foreach ($tablesToUpdate as $table => $column) {
                if (Schema::hasTable($table)) {
                    DB::table($table)
                        ->whereIn($column, $removeIds)
                        ->update([$column => $keepId]);

                    $updatedCount = DB::table($table)
                        ->where($column, $keepId)
                        ->count();

                    \Log::info("Updated $updatedCount records in $table for organization $keepId");
                }
            }

            // Delete duplicate organizations
            DB::table('organizations')
                ->whereIn('id', $removeIds)
                ->delete();

            \Log::info("Organization deduplication: keeping ID $keepId, removing IDs " . implode(',', $removeIds));
        }

        // Add unique constraint to prevent future duplicates
        Schema::table('organizations', function ($table) {
            $table->unique('slug');
        });
    }

    public function down()
    {
        // Remove unique constraint
        Schema::table('organizations', function ($table) {
            $table->dropUnique(['slug']);
        });

        // Note: We cannot restore deleted duplicates
        \Log::warning('Organization deduplication cannot be fully reversed. Unique constraint removed.');
    }
};
