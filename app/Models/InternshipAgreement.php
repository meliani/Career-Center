<?php

namespace App\Models;

use App\Enums;
use App\Filament\Administration\Resources;
use Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;

class InternshipAgreement extends Core\BackendBaseModel
{
    use SoftDeletes;

    protected $table = 'internships';

    protected static $settings;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new Scopes\InternshipAgreementScope());
    }

    public function __construct(?\App\Settings\NotificationSettings $settings = null)
    {
        self::$settings = $settings;
    }

    protected static function booted(): void
    {

        // if (env('EMAIL_NOTIFICATIONS', false)) {
        // if (self::$settings->in_app) {
        static::updated(function ($agreement) {
            if ($agreement->wasChanged('status')) {
                Filament\Notifications\Notification::make()
                    ->title(__('The status of an Internship Agreement has changed.'))
                    ->success()
                    ->icon('heroicon-s-check-circle')
                    ->body('body')
                    ->actions([
                        Filament\Notifications\Actions\Action::make(__('View'))
                            ->button()
                            ->url(Resources\InternshipAgreementResource::getUrl('view', [$agreement->id]))
                            ->size('xs')
                            ->outlined()
                            ->color('success'),
                        Filament\Notifications\Actions\Action::make(__('Edit'))
                            ->button()
                            ->size('xs')
                            ->color('primary')
                            ->outlined()
                            ->url(Resources\InternshipAgreementResource::getUrl('edit', [$agreement->id])),

                    ])
                    ->sendToDatabase(User::Administrators());
            }
        });

        static::created(function ($agreement) {
            Filament\Notifications\Notification::make()
                ->title(__('A new Internship Agreement has been created.'))
                ->success()
                ->icon('heroicon-s-check-circle')
                ->body('body')
                ->actions([
                    Filament\Notifications\Actions\Action::make(__('View'))
                        ->button()
                        ->size('xs')
                        ->color('success')
                        ->markAsRead()
                        ->url('url'),
                    Filament\Notifications\Actions\Action::make(__('Mark As Unread'))
                        ->button()
                        ->size('xs')
                        ->color('danger')
                        ->markAsUnread(),
                ])
                ->sendToDatabase(User::Administrators());
        });
        // }
        // if (self::$settings->by_email) {
        static::updated(
            function ($agreement) {
                if ($agreement->wasChanged('status')) {
                    User::Administrators()->each(
                        function ($admin) use ($agreement) {
                            $admin->notify(new \App\Notifications\InternshipAgreementStatusChanged($agreement));
                        }
                    );
                    $agreement->student->notify(new \App\Notifications\InternshipAgreementStatusChanged($agreement));
                }
            }
        );
        static::created(function ($agreement) {
            $agreement->student->notify(new \App\Notifications\InternshipAgreementAnnounced($agreement));
            User::Administrators()->each(
                function ($admin) use ($agreement) {
                    $admin->notify(new \App\Notifications\InternshipAgreementAnnounced($agreement));
                }
            );
        });

        // }
    }

    public $fillable = [
        'id_pfe',
        'organization_name',
        'central_organization',
        'adresse',
        'city',
        'country',
        'office_location',
        'parrain_titre',
        'parrain_nom',
        'parrain_prenom',
        'parrain_fonction',
        'parrain_tel',
        'parrain_mail',
        'encadrant_ext_titre',
        'encadrant_ext_nom',
        'encadrant_ext_prenom',
        'encadrant_ext_fonction',
        'encadrant_ext_tel',
        'encadrant_ext_mail',
        'title',
        'description',
        'keywords',
        'starting_at',
        'ending_at',
        'remuneration',
        'currency',
        'load',
        'int_adviser_name',
        'student_id',
        'year_id',
        'project_id',
        'status',
        'announced_at',
        'validated_at',
        'assigned_department',
        'received_at',
        'signed_at',
        'project_id',
        'observations',
        'active',
    ];

    protected $casts = [
        'starting_at' => 'date',
        'ending_at' => 'date',
        'validated_at' => 'datetime',
        'signed_at' => 'datetime',
        'received_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_active' => 'boolean',
        'is_mobility' => 'boolean',
        'status' => Enums\Status::class,
        'parrain_titre' => Enums\Title::class,
        'encadrant_ext_titre' => Enums\Title::class,
        'assigned_department' => Enums\Department::class,
        'teammate_status' => Enums\TeammateStatus::class,
        'active' => 'boolean',

        // 'status' => 'string',
        // 'parrain_titre' => 'string',
        // 'encadrant_ext_titre' => 'string',
        // 'assigned_department' => 'string',
        // 'teammate_status' => 'string',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // public function projects()
    // {
    //     return $this->belongsTo(Project::class);
    // }

    public function validate(?string $department = null)
    {
        try {
            if (Gate::denies('validate-internship', $this)) {
                throw new AuthorizationException();
            }

            $this->validated_at = now();
            $this->status = Enums\Status::Validated;
            // $this->department =$department;
            $this->save();
            Filament\Notifications\Notification::make()
                ->title('Saved successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            Filament\Notifications\Notification::make()
                ->title('Sorry You must be a Program Coordinator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }

    public function sign()
    {
        try {
            if (Gate::denies('sign-internship', $this)) {
                throw new AuthorizationException();
            }
            $this->signed_at = now();
            $this->status = Enums\Status::Signed;
            $this->save();
            Filament\Notifications\Notification::make()
                ->title('Signed successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            Filament\Notifications\Notification::make()
                ->title('Sorry You must be an Administrator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }

    public function receive()
    {
        try {
            if (Gate::denies('sign-internship', $this)) {
                throw new AuthorizationException();
            }
            $this->received_at = now();
            $this->status = Enums\Status::Completed;
            $this->save();
            Filament\Notifications\Notification::make()
                ->title('Achieved successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            Filament\Notifications\Notification::make()
                ->title('Sorry You must be an Administrator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }

    public function assignDepartment($department)
    {
        try {
            if (Gate::denies('validate-internship', $this)) {
                throw new AuthorizationException();
            }
            $this->assigned_department = $department;
            $this->save();
            Filament\Notifications\Notification::make()
                ->title('Assigned successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            Filament\Notifications\Notification::make()
                ->title('Sorry You must be a Program Coordinator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }

    public function changeStatus($status)
    {
        try {
            if (Gate::denies('validate-internship', $this)) {
                throw new AuthorizationException();
            }
            $this->status = $status;
            $this->save();
            Filament\Notifications\Notification::make()
                ->title('Status changed successfully')
                ->success()
                ->send();
        } catch (AuthorizationException $e) {

            Filament\Notifications\Notification::make()
                ->title('Sorry You must be a Program Coordinator.')
                ->danger()
                ->send();

            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
    }

    public function getParrainNameAttribute()
    {
        return $this->parrain_nom . ' ' . $this->parrain_prenom;
    }

    public function getEncadrantExtNameAttribute()
    {
        return $this->encadrant_ext_nom . ' ' . $this->encadrant_ext_prenom;
    }

    public function getDureeAttribute()
    {
        return $this->ending_at->diffInWeeks($this->starting_at) . ' semaines';
    }

    public function getDurationInMonthsAttribute()
    {
        return $this->ending_at->diffInMonths($this->starting_at) . ' mois';
    }

    public function scopeSigned($query)
    {
        return $query->whereNotNull('signed_at');
    }

    public function parseKeywords()
    {
        $text = $this->keywords; // Assuming the original keywords are stored in a `keywords` attribute
        $lines = explode("\n", $text);
        $allKeywords = [];

        foreach ($lines as $line) {
            $keywords = explode(',', $line);
            foreach ($keywords as $keyword) {
                $cleanKeyword = trim($keyword);
                if (! empty($cleanKeyword)) {
                    $allKeywords[] = strtolower($cleanKeyword); // Normalize to lowercase
                }
            }
        }

        $uniqueKeywords = array_unique($allKeywords);
        $tags = json_encode($uniqueKeywords, JSON_UNESCAPED_UNICODE); // Convert to JSON array for storage

        $this->tags = $tags; // Assuming the new column is named `tags`
        $this->save();
    }
}
