<?
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Title: string implements HasLabel
{
    case Mrs = 'Mrs.';
    case Mr = 'Mr.';

    public function getLabel(): ?string
    {
        return __($this->name);
    }
}
