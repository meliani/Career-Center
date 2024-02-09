<?php

use function Pest\Livewire\livewire;
 
livewire(\App\Filament\Administration\Resources\InternshipAgreementResource\Pages\CreateInternshipAgreement::class)
    ->fillForm([
        'title' => fake()->sentence(),
        // ...
    ]);