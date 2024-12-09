<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update from internship agreements
        DB::statement('
            UPDATE projects p
            INNER JOIN project_agreements pa ON p.id = pa.project_id
            INNER JOIN internship_agreements ia ON pa.agreeable_id = ia.id
            INNER JOIN internship_agreement_contacts ac_parrain ON ac_parrain.id = ia.parrain_id
            INNER JOIN internship_agreement_contacts ac_supervisor ON ac_supervisor.id = ia.external_supervisor_id
            SET
                p.organization_id = ia.organization_id,
                p.parrain_id = ac_parrain.id,
                p.external_supervisor_id = ac_supervisor.id
        ');
    }

    public function down()
    {
        DB::table('projects')
            ->update([
                'organization_id' => null,
                'parrain_id' => null,
                'external_supervisor_id' => null,
            ]);
    }
};
