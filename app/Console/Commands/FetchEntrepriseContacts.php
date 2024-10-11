<?php

namespace App\Console\Commands;

use App\Enums\EntrepriseContactCategory;
use App\Models\EntrepriseContacts;
use App\Models\InternshipAgreement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class FetchEntrepriseContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carrieres:fetch-entreprise-contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch entreprise contacts from Internship Agreements supervisors and parrains';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        /*
        ** InternshipAgreements Attributes**
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
        'year_id',
        'organization_name',
        'central_organization',
        */

        /*
        **Entreprise contact attributes**
        'email',
        'title',
        'first_name',
        'last_name',
        'company',
        'position',
        'alumni_promotion',
        'category',
        'years_of_interactions_with_students',
        'number_of_bounces',
        'bounce_reason',
        'is_account_disabled',
        'last_time_contacted',
        'last_year_id_supervised',
        'interactions_count'
        */

        // we gonna Fetch entrepriseContacts model from InternshipAgreements records
        $internshipAgreements = InternshipAgreement::all();

        foreach ($internshipAgreements as $internshipAgreement) {

            // Validate email format
            $email = $internshipAgreement->parrain_mail;
            $validator = Validator::make(['email' => $email], [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                // Handle invalid email format
                continue;
            }

            // Check DNS records
            $domain = substr(strrchr($email, '@'), 1);
            if (! checkdnsrr($domain, 'MX')) {
                // Handle invalid email domain
                continue;
            }

            // Check if an EntrepriseContact with the same email already exists
            $existingContact = EntrepriseContacts::where('email', $internshipAgreement->parrain_mail)
                ->where('category', EntrepriseContactCategory::Parrain)
                ->first();
            if (! $existingContact) {
                $entrepriseContact = new EntrepriseContacts;
                $entrepriseContact->email = $internshipAgreement->parrain_mail;
                $entrepriseContact->title = $internshipAgreement->parrain_titre;
                $entrepriseContact->first_name = $internshipAgreement->parrain_prenom;
                $entrepriseContact->last_name = $internshipAgreement->parrain_nom;
                $entrepriseContact->company = $internshipAgreement->organization_name;
                $entrepriseContact->position = $internshipAgreement->parrain_fonction;
                $entrepriseContact->category = EntrepriseContactCategory::Parrain;
                $entrepriseContact->number_of_bounces = 0;
                $entrepriseContact->bounce_reason = null;
                $entrepriseContact->is_account_disabled = false;
                $entrepriseContact->last_year_id_supervised = $internshipAgreement->year_id;
                $entrepriseContact->first_year_id_supervised = $internshipAgreement->year_id;
                $entrepriseContact->interactions_count = 1;

                $entrepriseContact->save();
            } else {

                // Update only if the new text is greater than the existing text

                if (strlen($internshipAgreement->parrain_prenom) > strlen($existingContact->first_name)) {
                    $existingContact->first_name = $internshipAgreement->parrain_prenom;
                }
                if (strlen($internshipAgreement->parrain_nom) > strlen($existingContact->last_name)) {
                    $existingContact->last_name = $internshipAgreement->parrain_nom;
                }
                if (strlen($internshipAgreement->organization_name) > strlen($existingContact->company)) {
                    $existingContact->company = $internshipAgreement->organization_name;
                }
                if (strlen($internshipAgreement->parrain_fonction) > strlen($existingContact->position)) {
                    $existingContact->position = $internshipAgreement->parrain_fonction;
                }
                // $existingContact->number_of_bounces = 0;
                // $existingContact->bounce_reason = null;
                // $existingContact->is_account_disabled = false;
                $existingContact->last_year_id_supervised = $internshipAgreement->year_id;
                // increment only if last year is different from the current year
                if ($existingContact->last_year_id_supervised != $internshipAgreement->year_id) {
                    $existingContact->increment('interactions_count');
                }

                $existingContact->save();
            }

            // Repeat the same process for encadrant_ext_mail
            $email = $internshipAgreement->encadrant_ext_mail;
            $validator = Validator::make(['email' => $email], [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                // Handle invalid email format
                continue;
            }

            $domain = substr(strrchr($email, '@'), 1);
            if (! checkdnsrr($domain, 'MX')) {
                // Handle invalid email domain
                continue;
            }

            $existingContact = EntrepriseContacts::where('email', $internshipAgreement->encadrant_ext_mail)
                ->where('category', EntrepriseContactCategory::Supervisor)
                ->first();

            if (! $existingContact) {
                $entrepriseContact = new EntrepriseContacts;
                $entrepriseContact->email = $internshipAgreement->encadrant_ext_mail;
                $entrepriseContact->title = $internshipAgreement->encadrant_ext_titre;
                $entrepriseContact->first_name = $internshipAgreement->encadrant_ext_prenom;
                $entrepriseContact->last_name = $internshipAgreement->encadrant_ext_nom;
                $entrepriseContact->company = $internshipAgreement->organization_name;
                $entrepriseContact->position = $internshipAgreement->encadrant_ext_fonction;
                $entrepriseContact->category = EntrepriseContactCategory::Supervisor;
                $entrepriseContact->number_of_bounces = 0;
                $entrepriseContact->bounce_reason = null;
                $entrepriseContact->is_account_disabled = false;
                $entrepriseContact->last_year_id_supervised = $internshipAgreement->year_id;
                $entrepriseContact->first_year_id_supervised = $internshipAgreement->year_id;
                $entrepriseContact->interactions_count = 1;

                $entrepriseContact->save();
            } else {

                // Update only if the new text is greater than the existing text

                if (strlen($internshipAgreement->encadrant_ext_prenom) > strlen($existingContact->first_name)) {
                    $existingContact->first_name = $internshipAgreement->encadrant_ext_prenom;
                }
                if (strlen($internshipAgreement->encadrant_ext_nom) > strlen($existingContact->last_name)) {
                    $existingContact->last_name = $internshipAgreement->encadrant_ext_nom;
                }
                if (strlen($internshipAgreement->organization_name) > strlen($existingContact->company)) {
                    $existingContact->company = $internshipAgreement->organization_name;
                }
                if (strlen($internshipAgreement->encadrant_ext_fonction) > strlen($existingContact->position)) {
                    $existingContact->position = $internshipAgreement->encadrant_ext_fonction;
                }
                // $existingContact->number_of_bounces = 0;
                // $existingContact->bounce_reason = null;
                // $existingContact->is_account_disabled = false;
                $existingContact->last_year_id_supervised = $internshipAgreement->year_id;
                if ($existingContact->last_year_id_supervised != $internshipAgreement->year_id) {
                    $existingContact->increment('interactions_count');
                }
                $existingContact->save();
            }

        }

    }
}
