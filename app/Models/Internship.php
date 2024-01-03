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
    
    // public $fillable = [
    //     'id',
    //     'raison_sociale',
    //     'adresse',
    //     'ville',
    //     'pays',
    //     'office_location',
    //     'parrain_titre',
    //     'parrain_nom',
    //     'parrain_prenom',
    //     'parrain_fonction',
    //     'parrain_tel',
    //     'parrain_mail',
    //     'encadrant_ext_titre',
    //     'encadrant_ext_nom',
    //     'encadrant_ext_prenom',
    //     'encadrant_ext_fonction',
    //     'encadrant_ext_tel',
    //     'encadrant_ext_mail',
    //     'intitule',
    //     'descriptif',
    //     'keywords',
    //     'starting_at',
    //     'ending_at',
    //     'abroad',
    //     'remuneration',
    //     'currency',
    //     'load',
    //     'abdoard_school',
    //     'int_adviser_id',
    //     'int_adviser_name',
    //     'is_signed',
    //     'student_id',
    //     'year_id',
    //     'is_valid',
    //     'model_status_id',
    //     'status',
    //     'procedure_achieved_at',
    //     'pedagogic_validation_date',
    //     'meta_pedagogic_validation',
    //     'adviser_validated_at',
    //     'meta_adviser_validation',
    //     'administration_signed_at',
    //     'meta_administration_signature',
    //     'notes',
    //     'notes->agent_id',
    //     'notes->note',
    // ];

    protected $casts = [
        // add datetime fields here
        'reviewed_at' => 'datetime',
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
        'deleted_at'=> 'datetime',    
    ];
    
    public function binome()
    {
        return $this->belongsTo(Student::class,'binome_user_id','id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getParrainNameAttribute()
	{
		return $this->getTitle($this->parrain_titre).' '.$this->parrain_nom.' '.$this->parrain_prenom;
    }
    public function getEncadrantExtNameAttribute()
	{
		return $this->getTitle($this->encadrant_ext_titre).' '.$this->encadrant_ext_nom.' '.$this->encadrant_ext_prenom;
    }

    public function getDureeAttribute()
	{
        return $this->ending_at->diffInWeeks($this->starting_at).' semaines';
    }
    public function getDurationInMonthsAttribute()
	{
        return $this->ending_at->diffInMonths($this->starting_at).' mois';
    }

}
