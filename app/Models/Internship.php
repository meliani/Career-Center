<?php

namespace App\Models;

use App\Models\Core\baseModel;
use App\Models\Person;
use App\Models\Professor;
use App\Models\Student;
use App\Models\School\Internship\Adviser;
use App\Models\School\Internship\Advising;
use App\Models\School\Internship\Defense;
use App\Models\School\Project\Team;
use App\Models\User;
use Carbon\Carbon;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\School\Project\Project;

class Internship extends baseModel
{

    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
    }

    protected $guarded = [];

    protected $casts = [
        // add datetime fields here
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    public function review()
    {
        $this->reviewed_at = now();
        $this->save();
    }
    public function binome()
    {
        return $this->belongsTo(Student::class, 'binome_user_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getParrainNameAttribute()
    {
        return $this->getTitle($this->parrain_titre) . ' ' . $this->parrain_nom . ' ' . $this->parrain_prenom;
    }
    public function getEncadrantExtNameAttribute()
    {
        return $this->getTitle($this->encadrant_ext_titre) . ' ' . $this->encadrant_ext_nom . ' ' . $this->encadrant_ext_prenom;
    }

    public function getDureeAttribute()
    {
        return $this->ending_at->diffInWeeks($this->starting_at) . ' semaines';
    }
    public function getDurationInMonthsAttribute()
    {
        return $this->ending_at->diffInMonths($this->starting_at) . ' mois';
    }
}
