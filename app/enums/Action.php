<?

use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasLabel
{
    case PendingAnnouncement = 'Pending Announcement';
    case PendingReview = 'Pending Review';
    case PendingApproval = 'Pending Approval';
    case PendingSignOff = 'Pending Sign Off';
    case PendingStart = 'Pending Start';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}