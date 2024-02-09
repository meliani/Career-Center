<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class Offer extends Core\FrontendBaseModel
{
    protected $table = 'internship_offers';

    use SoftDeletes;

    protected $append = [
        'expire_at_human_readable',
    ];

    // non fillable fields
    protected $guarded = [];
    // public $fillable = [
    //     'id',
    //     'year_id',
    //     'level',
    //     'organization_name',
    //     'country',
    //     'internship_type',
    //     'responsible_fullname',
    //     'responsible_occupation',
    //     'responsible_phone',
    //     'responsible_email',
    //     'project_title',
    //     'project_details',
    //     'internship_location',
    //     'keywords',
    //     'attached_file',
    //     'link',
    //     'internship_duration',
    //     'remuneration',
    //     'currency',
    //     'recruting_type',
    //     'application_email',
    //     'event_id',
    //     'event_date',
    //     'badge',
    //     'display_permissions',
    //     'status',
    //     'is_valid',
    //     'applyable',
    //     'contact_email',
    //     'expire_at',
    // ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        //     'id' => 'string',
        //     'year_id' => 'string',
        //     'level' => 'string',
        //     'organization_name' => 'string',
        'internship_type' => 'string',
        //     'responsible_fullname' => 'string',
        //     'responsible_occupation' => 'string',
        //     'responsible_phone' => 'string',
        //     'responsible_email' => 'string',
        //     'project_title' => 'string',
        //     'project_details' => 'string',
        //     'internship_location' => 'string',
        //     'keywords' => 'string',
        //     'attached_file' => 'string',
        //     'link' => 'string',
        //     'remuneration' => 'string',
        'recruting_type' => 'string',
        'internship_duration' => 'string',

        //     'event_id' => 'string',
        //     'event_date' => 'string',
        //     'badge' => 'string',
        //     'display_permissions' => 'string',
        //     'status' => 'integer',
        //     'is_valid' => 'boolean',
        //     'applyable' => 'boolean',
        'expire_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    //     protected $guarded =[
    // // '_token'
    //     ];

    public function setAttachedFileAttribute($value)
    {
        //dd($value);
        $this->attributes['attached_file'] = Storage::disk('interOffersDocs')->putFile('', new File($value));
    }

    public function getResponsibleNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function getProjectDetailAttribute($value)
    {
        return nl2br($value);
    }

    public function getInternshipLocationAttribute($value)
    {
        return nl2br($value);
    }

    public function getAttachedFileAttribute($value)
    {
        if ($value != null) {
            return $value;
        } else {
            return null;
        }
    }

    /**
     * Get the Year for the Offer.
     */
    public function program()
    {
        return $this->belongsTo(\App\Models\School\Program::class);
    }

    public function year()
    {
        return $this->belongsTo(\App\Models\School\Year::class);
    }

    public function applications()
    {
        return $this->hasMany('App\Models\School\InternshipAgreement\Application', 'offre_de_stage_id', 'id');
    }

    public function getExpireAtHumanReadableAttribute()
    {
        //Carbon::now();
        $date = $this->attributes['expire_at'];
        if (isset($date)) {
            $expired_at = \Carbon\Carbon::parse($date);
            $elapse = $expired_at->diffInDays();
            if (\Carbon\Carbon::now() < $expired_at) {
                //if expiring diffInHours ->diffForHumans()
                return 'Expire '.$expired_at->diffForHumans();
            } elseif (\Carbon\Carbon::now() > $expired_at) {
                return 'Expiré';
            }
            //Here have to carbon now - expire_at in days
            //if expired echo expired
            else {
                return null;
            }
        } else {
            return null;
        }
    }
}
