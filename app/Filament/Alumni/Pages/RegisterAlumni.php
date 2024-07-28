<?php

namespace App\Filament\Alumni\Pages;

use App\Enums;
use App\Models\Alumni;
use App\Models\AlumniReference;
use App\Notifications\AlumniAccountCreated;
use App\Notifications\AlumniAccountRequestCreated;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;

class RegisterAlumni extends BaseRegister
{
    public ?array $data = [];

    protected string $userModel;

    public function register(): ?RegistrationResponse
    {
        // try {
        //     $this->rateLimit(2);
        // } catch (TooManyRequestsException $exception) {
        //     $this->getRateLimitedNotification($exception)?->send();

        //     return null;
        // }
        // dd($this->form->getState());

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        $alumniReference = AlumniReference::where('name', $data['name'])->first();

        if ($alumniReference) {
            $alumni = $this->getUserModel()::create($data);
            $alumni->notify(new AlumniAccountCreated($alumni));

            return $alumni;
        } else {
            $alumniAccountRequest = Alumni::create($data);
            $alumniAccountRequest->notify(new AlumniAccountRequestCreated($alumniAccountRequest));

            return $alumniAccountRequest;
        }

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->columns(2)
                    ->schema([
                        ToggleButtons::make('title')
                            ->options(Enums\Title::class)
                            ->required()
                            ->inline()
                            ->columnSpanFull(),
                        // TextInput::make('first_name')
                        //     ->required()
                        //     ->maxLength(255),
                        // TextInput::make('last_name')
                        //     ->required()
                        //     ->maxLength(255),
                        $this->getNameFormComponent()
                            ->Placeholder(__('Full Name'))
                            ->columnSpanFull(),

                    ]),
                $this->getEmailFormComponent()
                    ->label('Email')
                    ->Placeholder('email@email.com'),
                // ->endsWith(['@ine.inpt.ac.ma']),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                // ToggleButtons::make('degree')
                //     ->options(Enums\AlumniDegree::class)
                //     ->inline()
                //     ->required(),
                Forms\Components\Group::make()
                    ->columns(2)
                    ->schema([
                        Select::make('program')
                            ->options(Enums\Program::class)
                            ->required(),
                        Select::make('graduation_year_id')
                            ->label(__('Graduation year'))
                            ->options(\App\Models\Year::all()->pluck('title', 'id')->toArray()),
                    ]),
                // TextInput::make('email_perso')
                //     ->email()
                //     ->required()
                //     ->maxLength(255),
                TextInput::make('phone_number')
                    ->label(__('Phone number (international format)'))
                    ->required()
                    ->Placeholder('+2126XXXXXXXX')
                    ->rule('phone'),

            ]);
    }
}
