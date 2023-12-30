<?

use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasLabel
{
    case Draft = 'Draft';
    case Anounced = 'Anounced';
    case Rejected = 'Rejected';
    case Reviewed = 'Reviewed';
    case Approved = 'Approved';
    case Declined = 'Declined';
    case SignedOff = 'Signed Off';
    case Started = 'Started';
    case Completed = 'Completed';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}