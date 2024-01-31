<?php

// declare(strict_types=1);

namespace App\Services;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Spatie\Period\PeriodCollection;
use Spatie\Period\Period;
use Spatie\Period\Precision;
use Cmixin\EnhancedPeriod;
use Carbon\Carbon;


final class SlotSplitter extends PeriodCollection
{
    private PeriodCollection $slots;

    public function __construct(PeriodCollection|Period $slots)
    {
        if ($slots instanceof Period) {
            $slots = new PeriodCollection($slots);
        }
        $this->slots = $slots;

    }

    /** @return array<int, CarbonInterface|null> */
    public function split(int $minutes)
    // : PeriodCollection
    {
        // dd($this->slots);
        // $splitPeriods = [];
        // $interval = sprintf('%d minutes', $minutes);
        // foreach ($this->slots as $item) {
        //     $period = CarbonPeriod::create($item->start(), '1 hour', $item->end())->toEnhancedPeriod();
        //     // $carbonPeriod = Period::make($item->start(), $item->end(), Precision::Minute());
        //     // $carbonPeriod->excludeEndDate();
        //     dd($period);

        //     foreach ($period as $timeslot) {
        //         $splitPeriods[$timeslot->timestamp] = $period;
        //     }
        // }
        // ksort($splitPeriods);


        // return PeriodCollection::make(...array_values($splitPeriods));
        $splitPeriods = [];

        // Define the start and end times of the day and lunch time
        $dayStartsAt = Carbon::createFromTime(8, 0); // 8:00 AM
        $dayEndsAt = Carbon::createFromTime(17, 0); // 5:00 PM
        $lunchStartsAt = Carbon::createFromTime(12, 0); // 12:00 PM
        $lunchEndsAt = Carbon::createFromTime(13, 0); // 1:00 PM
    
        // Create a CarbonPeriod for the morning and afternoon
        $morning = CarbonPeriod::create($dayStartsAt, $minutes . ' minutes', $lunchStartsAt);
        $afternoon = CarbonPeriod::create($lunchEndsAt, $minutes . ' minutes', $dayEndsAt);
    
        // Split each period into timeslots and store them in the $splitPeriods array
        foreach ([$morning, $afternoon] as $period) {
            foreach ($period as $timeslot) {
                $splitPeriods[] = Period::make(
                    $timeslot->copy()->startOfMinute(),
                    $timeslot->copy()->addMinutes($minutes)->subSecond()
                );
            }
        }
        return new PeriodCollection(...$splitPeriods);
    }


}