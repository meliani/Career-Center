<?php

namespace App\Services;

use App\Models\DefenseSchedule;
use App\Models\InternshipAgreement;
use App\Models\Project;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use App\Models\Professor;
use App\Models\Jury;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Actions\Action;
use App\Models\Student;

class StudentService
{
    private $currentPin;
    private $streamOrder;

    public static function setPin(Student $student, $currentPin, $streamOrder){
        $student->pin = $streamOrder.str_pad($currentPin, 2, '0', STR_PAD_LEFT);
        $student->save();
    }

    public function generatePfeIds($students)
    {
        $students->each(function($student){
            $this->currentPin++;
            echo " current pin : ". $this->currentPin ." - ".$this->streamOrder."<br>" ;
            // $student->setPin($student, $this->currentPin,$streamOrder);
            $student->pin = $this->streamOrder.str_pad($this->currentPin, 2, "0", STR_PAD_LEFT);
            $student->save();
            }
        );

    }
}
