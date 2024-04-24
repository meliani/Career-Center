<?php

namespace App\Models;

class Year extends Core\BackendBaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime',
    ];

    public function actual()
    {
        return 7;
    }

    public static function current()
    {
        return Year::latest()->first();
    }

    /**
     * Get the Internships for the Year.
     */
    public function internships()
    {
        return $this->belongsToMany(InternshipAgreement::class);
    }
}
