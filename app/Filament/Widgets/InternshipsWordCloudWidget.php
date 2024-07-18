<?php

namespace App\Filament\Widgets;

use App\Models\InternshipAgreement;
use Filament\Widgets\Widget;

class InternshipsWordCloudWidget extends Widget
{
    protected static string $view = 'filament.widgets.internships-word-cloud';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return true;
        // return ! auth()->user()->is_verified;
    }

    protected function getData(): array
    {
        $keywords = []; // Initialize an array to hold keyword frequencies

        $internshipAgreements = InternshipAgreement::all();
        foreach ($internshipAgreements as $agreement) {
            $tags = json_decode($agreement->tags, true);
            foreach ($tags as $tag) {
                if (! isset($keywords[$tag])) {
                    $keywords[$tag] = 0;
                }
                $keywords[$tag]++;
            }
        }

        return [
            'keywords' => $keywords,
        ];
    }

    public function render(): \Illuminate\View\View
    {
        return view(static::$view, $this->getData());
    }
}
