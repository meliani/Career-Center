<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\baseModel;

class Program extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'short_name', 'long_name', 'order'
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
    ];

}
