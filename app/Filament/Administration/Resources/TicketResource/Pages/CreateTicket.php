<?php

namespace App\Filament\Administration\Resources\TicketResource\Pages;

use App\Filament\Administration\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;
}
