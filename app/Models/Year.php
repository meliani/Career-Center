<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\baseModel;

class Year extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    protected $casts = [
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
        'date'=> 'datetime',    
    ];
    public function actual()
    {
        return 6;
    }

    /**
     * Get the Internships for the Year.
     */
    public function internships()
    {
        return $this->belongsToMany(\App\Internship::class);
    }

}
