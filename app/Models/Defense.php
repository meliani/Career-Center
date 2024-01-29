<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Defense extends Model
{

    public function internships()
    {
        return $this->belongsToMany(Internship::class, 'defense_internship');
    }

}