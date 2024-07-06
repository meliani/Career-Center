<?php

namespace App\Services;

use Filament\Support\Colors\Color;

class ColorService
{
    public static function colorsOfTheDay()
    {
        $dayOfWeek = now()->dayOfWeek; // 0 (for Sunday) through 6 (for Saturday)

        $colorsByDay = [
            0 => [ // Sunday
                'danger' => Color::Red,
                'gray' => Color::Stone,
                'info' => Color::Teal,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'secondary' => Color::Violet,
            ],
            1 => [ // Monday
                'danger' => Color::Pink,
                'gray' => Color::Gray,
                'info' => Color::Sky,
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                'secondary' => Color::Cyan,
            ],
            2 => [ // Tuesday
                'danger' => Color::Orange,
                'gray' => Color::Slate,
                'info' => Color::Cyan,
                'primary' => Color::Violet,
                'success' => Color::Lime,
                'warning' => Color::Amber,
                'secondary' => Color::Purple,
            ],
            3 => [ // Wednesday
                'danger' => Color::Fuchsia,
                'gray' => Color::Neutral,
                'info' => Color::Sky,
                'primary' => Color::Indigo,
                'success' => Color::Green,
                'warning' => Color::Orange,
                'secondary' => Color::Teal,
            ],
            4 => [ // Thursday
                'danger' => Color::Red,
                'gray' => Color::Zinc,
                'info' => Color::Teal,
                'primary' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Yellow,
                'secondary' => Color::Cyan,
            ],
            5 => [ // Friday
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'secondary' => Color::Sky,
            ],
            6 => [ // Saturday
                'danger' => Color::Fuchsia,
                'gray' => Color::Stone,
                'info' => Color::Cyan,
                'primary' => Color::Indigo,
                'success' => Color::Lime,
                'warning' => Color::Orange,
                'secondary' => Color::Purple,
            ],
        ];

        return $colorsByDay[$dayOfWeek];
    }

    public static function studentsAppColorsOfTheDay()
    {
        $dayOfWeek = now()->dayOfWeek; // 0 (for Sunday) through 6 (for Saturday)

        $colorsByDay = [
            0 => [ // Sunday
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'primary' => Color::Violet,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'secondary' => Color::Indigo,
            ],
            1 => [ // Monday
                'danger' => Color::Red,
                'gray' => Color::Gray,
                'info' => Color::Cyan,
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                'secondary' => Color::Teal,
            ],
            2 => [ // Tuesday
                'danger' => Color::Orange,
                'gray' => Color::Zinc,
                'info' => Color::Sky,
                'primary' => Color::Purple,
                'success' => Color::Lime,
                'warning' => Color::Amber,
                'secondary' => Color::Fuchsia,
            ],
            3 => [ // Wednesday
                'danger' => Color::Pink,
                'gray' => Color::Neutral,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'secondary' => Color::Teal,
            ],
            4 => [ // Thursday
                'danger' => Color::Red,
                'gray' => Color::Stone,
                'info' => Color::Sky,
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                'secondary' => Color::Cyan,
            ],
            5 => [ // Friday
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'secondary' => Color::Purple,
            ],
            6 => [ // Saturday
                'danger' => Color::Fuchsia,
                'gray' => Color::Neutral,
                'info' => Color::Cyan,
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Orange,
                'secondary' => Color::Indigo,
            ],
        ];

        return $colorsByDay[$dayOfWeek];
    }
}
