<?php

// namespace App\Filament\Administration\Resources;

// use App\Models\ProjectProfessor;
// use Filament\Forms\Components\RelationshipSelect;
// use Filament\Forms\Form;
// use Filament\Resources\Forms\Components\Select as FilamentSelect;
// use App\Filament\Core\BaseResource as Resource;
// use Filament\Tables\Columns\Text as TextColumn;
// use Filament\Tables\Table;

// class ProjectProfessorResource extends Resource
// {
//     // public static $model = 'App\\Models\\ProjectProfessor';
//     protected static ?string $model = ProjectProfessor::class;

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 RelationshipSelect::make('project_id')
//                     ->relationship('project', 'name')
//                     ->required(),
//                 RelationshipSelect::make('professor_id')
//                     ->relationship('professor', 'name')
//                     ->required(),
//                 FilamentSelect::make('role')
//                     ->options([
//                         'Supervisor' => 'Supervisor',
//                         'Reviewer' => 'Reviewer',
//                         'HeadOfJury' => 'HeadOfJury',
//                     ])
//                     ->required(),
//             ]);
//     }

//     public static function table(Table $table)
//     {
//         return $table
//             ->columns([
//                 TextColumn::make('project.name')
//                     ->label('Project'),
//                 TextColumn::make('professor.name')
//                     ->label('Professor'),
//                 TextColumn::make('role')
//                     ->label('Role'),
//             ]);
//     }
// public static function relations()
// {
//     return [
//         ProjectsRelationManager::class,
//     ];
// }
// }
