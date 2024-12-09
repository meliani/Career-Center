<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function findExistingContact($contact)
    {
        return DB::table('internship_agreement_contacts')
            ->where('organization_id', $contact->organization_id)
            ->where('first_name', $contact->first_name)
            ->where('last_name', $contact->last_name)
            ->where('email', $contact->email)
            ->first();
    }

    private function isBetterContact($existingContact, $newContact)
    {
        // Prefer contacts with phone numbers
        if (empty($existingContact->phone) && ! empty($newContact->phone)) {
            return true;
        }

        // If both have phone numbers or both don't, prefer the one with more complete data
        return strlen($newContact->function ?? '') > strlen($existingContact->function ?? '') ||
            strlen($newContact->title ?? '') > strlen($existingContact->title ?? '');
    }

    public function up()
    {
        // Store old ID to new ID mappings per table
        $idMappings = [
            'final_year_internship_contacts' => [],
            'apprenticeship_agreement_contacts' => [],
        ];

        // Map table names to their corresponding agreement tables
        $tableRelations = [
            'final_year_internship_contacts' => 'final_year_internship_agreements',
            'apprenticeship_agreement_contacts' => 'apprenticeship_agreements',
        ];

        foreach ($tableRelations as $contactsTable => $agreementsTable) {
            if (! Schema::hasTable($contactsTable)) {
                continue;
            }

            // Process each contact table separately
            DB::table($contactsTable)->orderBy('id')->chunk(100, function ($contacts) use ($contactsTable, &$idMappings) {
                foreach ($contacts as $contact) {
                    $existingContact = $this->findExistingContact($contact);

                    if ($existingContact) {
                        // If existing contact found, check if new one is better
                        if ($this->isBetterContact($existingContact, $contact)) {
                            // Update existing contact with better data
                            DB::table('internship_agreement_contacts')
                                ->where('id', $existingContact->id)
                                ->update([
                                    'role' => $contact->role,
                                    'title' => $contact->title,
                                    'function' => $contact->function,
                                    'phone' => $contact->phone,
                                    'updated_at' => now(),
                                ]);
                        }
                        // Map the old ID to the existing contact's ID
                        $idMappings[$contactsTable][$contact->id] = $existingContact->id;
                    } else {
                        // Insert new contact
                        $newId = DB::table('internship_agreement_contacts')->insertGetId([
                            'role' => $contact->role,
                            'title' => $contact->title,
                            'first_name' => $contact->first_name,
                            'last_name' => $contact->last_name,
                            'function' => $contact->function,
                            'phone' => $contact->phone,
                            'email' => $contact->email,
                            'organization_id' => $contact->organization_id,
                            'created_at' => $contact->created_at,
                            'updated_at' => $contact->updated_at,
                        ]);
                        $idMappings[$contactsTable][$contact->id] = $newId;
                    }
                }
            });

            // Update foreign keys immediately after processing each contact table
            if (Schema::hasTable($agreementsTable)) {
                foreach ($idMappings[$contactsTable] as $oldId => $newId) {
                    DB::table($agreementsTable)
                        ->where('parrain_id', $oldId)
                        ->update(['parrain_id' => $newId]);

                    DB::table($agreementsTable)
                        ->where('external_supervisor_id', $oldId)
                        ->update(['external_supervisor_id' => $newId]);
                }
            }
        }

        // Verify the mappings
        foreach ($tableRelations as $contactsTable => $agreementsTable) {
            if (Schema::hasTable($agreementsTable)) {
                // Log or check unmapped relationships
                $unmappedParrains = DB::table($agreementsTable)
                    ->whereNotNull('parrain_id')
                    ->whereNotIn('parrain_id', array_values($idMappings[$contactsTable]))
                    ->count();

                $unmappedSupervisors = DB::table($agreementsTable)
                    ->whereNotNull('external_supervisor_id')
                    ->whereNotIn('external_supervisor_id', array_values($idMappings[$contactsTable]))
                    ->count();

                if ($unmappedParrains > 0 || $unmappedSupervisors > 0) {
                    \Log::warning("Unmapped contacts found in $agreementsTable: $unmappedParrains parrains, $unmappedSupervisors supervisors");
                }
            }
        }
    }

    public function down()
    {
        DB::table('internship_agreement_contacts')->truncate();
    }
};
