<?php

namespace App\Filament\Administration\Resources\ProjectResource\Pages;

use App\Filament\Administration\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;
}
