<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InternshipAgreement;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\CorePolicy;
use Illuminate\Support\Facades\Gate;


class TimeslotPolicy extends CorePolicy
{

}
