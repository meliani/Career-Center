<?php

namespace App\Services;

use App\Enums;
use App\Models\Apprenticeship;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class ApprenticeshipValidator
{
    /**
     * Validates the apprenticeship data. Throws \Exception on failure.
     *
     * @param array $data
     * @throws \Exception
     */
    public static function validate(array $data): void
    {
        // Validate internship period
        if (!empty($data['starting_at']) && !empty($data['ending_at'])) {
            $startingAt = $data['starting_at'] instanceof Carbon ? $data['starting_at'] : Carbon::parse($data['starting_at']);
            $endingAt = $data['ending_at'] instanceof Carbon ? $data['ending_at'] : Carbon::parse($data['ending_at']);

            $validStartDate = Carbon::create($startingAt->year, 5, 15);
            $validEndDate = Carbon::create($endingAt->year, 8, 1);

            if ($startingAt->lt($validStartDate)) {
                throw new \Exception('The apprenticeship cannot start before May 15th.');
            }
            if ($endingAt->gt($validEndDate)) {
                throw new \Exception('The apprenticeship cannot end after July 31st.');
            }
            $weeks = ceil($startingAt->floatDiffInRealWeeks($endingAt));
            if ($weeks > 8) {
                throw new \Exception('The internship period cannot exceed 8 weeks.');
            }
        }
    }
}
