<?
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasLabel
{
    case Draft = 'Draft';
    case Announced = 'Announced';
    case Rejected = 'Rejected';
    case Validated = 'Validated';
    case Approved = 'Approved';
    case Declined = 'Declined';
    case SignedOff = 'Signed Off';
    case Started = 'Started'; // calculated
    case Completed = 'Completed'; // calculated

    public function getLabel(): ?string
    {
        return __($this->name);
    }
}