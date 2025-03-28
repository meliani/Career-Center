<?php

use App\Enums\SupervisionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // First find any foreign keys referencing professor_projects
        $foreignKeys = $this->getForeignKeysReferencingTable('professor_projects');
        
        // Drop any foreign keys that reference the professor_projects primary key
        foreach ($foreignKeys as $foreignKey) {
            Schema::table($foreignKey->table_name, function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey->constraint_name);
            });
        }

        // Check if professor_projects has foreign keys and drop them first
        Schema::table('professor_projects', function (Blueprint $table) {
            // Drop all foreign keys on this table to avoid constraint issues
            $foreignKeys = $this->getForeignKeysForTable('professor_projects');
            
            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign($foreignKey->constraint_name);
            }
        });
        
        // Now drop the primary key
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->dropPrimary(['professor_id', 'project_id']);
        });

        // Add the id column and set it as primary key
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->id()->first();
            
            // Add a unique constraint on professor_id and project_id
            $table->unique(['professor_id', 'project_id']);
        });

        // Set auto-incrementing IDs for existing records
        DB::statement('UPDATE professor_projects SET id = (SELECT @row := @row + 1 FROM (SELECT @row := 0) r)');
        
        // Recreate foreign keys that existed on this table
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->foreign('professor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
        });
        
        // Recreate foreign keys that referenced this table
        foreach ($foreignKeys as $foreignKey) {
            Schema::table($foreignKey->table_name, function (Blueprint $table) use ($foreignKey) {
                $table->foreign($foreignKey->column_name)
                      ->references('id') // Now reference the id column
                      ->on('professor_projects')
                      ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        // Drop foreign keys that reference professor_projects
        $foreignKeys = $this->getForeignKeysReferencingTable('professor_projects');
        foreach ($foreignKeys as $foreignKey) {
            Schema::table($foreignKey->table_name, function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey->constraint_name);
            });
        }
        
        // Drop foreign keys in the professor_projects table
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->dropForeign(['professor_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['approved_by']);
            
            // Drop the unique constraint
            $table->dropUnique(['professor_id', 'project_id']);
        });
        
        // Drop the id column and restore the composite primary key
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->primary(['professor_id', 'project_id']);
        });
        
        // Restore foreign keys
        Schema::table('professor_projects', function (Blueprint $table) {
            $table->foreign('professor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
        });
        
        // Restore foreign keys that referenced professor_projects
        foreach ($foreignKeys as $foreignKey) {
            Schema::table($foreignKey->table_name, function (Blueprint $table) use ($foreignKey) {
                // Recreate the original foreign keys (would need to adapt based on original constraints)
                $table->foreign($foreignKey->column_name)
                      ->references($foreignKey->referenced_column ?? 'professor_id')
                      ->on('professor_projects')
                      ->onDelete('cascade');
            });
        }
    }
    
    /**
     * Get foreign keys that reference a specific table
     */
    private function getForeignKeysReferencingTable($tableName)
    {
        return DB::select("
            SELECT 
                TABLE_NAME as table_name,
                COLUMN_NAME as column_name,
                CONSTRAINT_NAME as constraint_name,
                REFERENCED_COLUMN_NAME as referenced_column
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME = ?
        ", [$tableName]);
    }
    
    /**
     * Get foreign keys for a specific table
     */
    private function getForeignKeysForTable($tableName)
    {
        return DB::select("
            SELECT 
                CONSTRAINT_NAME as constraint_name
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
        ", [$tableName]);
    }
};
