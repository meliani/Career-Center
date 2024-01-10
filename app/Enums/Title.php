<?
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Title: string implements HasLabel
{
    case Male = 'Male';
    case Female = 'Female';

    public function getLabel(): ?string
    {
        return __($this->name);
    }
}
