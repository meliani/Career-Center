<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;
use App\Models\Internship;

class SignAction extends Action
{
    // protected ?string $name = null;

    // protected ?string $label = null;

    public static function getDefaultName(): string
    {
        return 'validate';
    }

    public function handle(Internship $internship): void
    {
        $internship->validate();
        // dd('SignAction action called');
    }
    public static function make(?string $name = null): static
    {

        /*         $instance = app(static::class);

        return Action::make($instance->name)
            ->label($instance->label)
            ->action(fn($record) => $instance($record)); */

        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, Internship $record): void {
            //  return carbon object with this format 2024-01-02 15:40:05, its a datetime format i mysql database
            $record->validated_at = Carbon::now()->format('yy-m-d H:i:s');
            $record->save();
        });

        return $static;
    }
}
