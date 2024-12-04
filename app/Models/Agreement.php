<?php

namespace App\Models;

interface Agreement
{
    public function getTitle(): string;

    public function getDescription(): string;

    public function getAssignedDepartment(): ?string;

    public function getOrganizationName(): string;

    public function getStudentName(): string;

    public function getStartDate(): ?\Carbon\Carbon;

    public function getEndDate(): ?\Carbon\Carbon;

    // ...other common methods...
}
