<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Status;
use App\Enums\Role;
use App\Enums\Department;
use App\Enums\Program;
use App\Enums\Title;


class baseModel extends Model
{
    protected $connection = 'frontend_database';
    // protected $dateFormat = 'd M Y';
    
    public function scopeActive($query) {
            return $query->where('is_active', true);
    }
    public function scopeValid($query) {
        return $query->where('is_valid', true);
    }
    public function scopePublished($query) {
        return $query->where('status', null);
    }
    public function scopeArchived($query) {
        return $query->where('status', -1);
    }

    /* Common methods */
    // static function getTitle($gender)
	// {
    //     if($gender==1)
    //     return "M.";
    //     elseif($gender==0)
    //     return "Mme";
    //     else
    //     return "Mme/M.";
    // }
}