<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefenseSync extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_soutenance',
        'heure',
        'autorisation',
        'lieu',
        'id_pfe',
        'nom_etudiant',
        'filiere',
        'encadrant_interne',
        'examinateur_1',
        'examinateur_2',
        'remarques',
        'convention_signee',
        'accord_encadrant_interne',
        'fiche_evaluation_entreprise',
    ];
}
