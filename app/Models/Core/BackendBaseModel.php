<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Status;
use App\Enums\Role;
use App\Enums\Department;
use App\Enums\Program;
use App\Enums\Title;


class BackendBaseModel extends Model
{
    protected $connection = 'backend_database';

}