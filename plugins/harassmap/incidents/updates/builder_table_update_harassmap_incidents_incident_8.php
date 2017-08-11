<?php namespace Harassmap\Incidents\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class BuilderTableUpdateHarassmapIncidentsIncident8 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_incident', function ($table) {
            $table->string('public_id', 20)->change();
            $table->unique('public_id');
        });
    }

    public function down()
    {
        Schema::table('harassmap_incidents_incident', function ($table) {
            $table->dropUnique('harassmap_incidents_incident_public_id_unique');
            $table->string('public_id', 100)->change();
        });
    }
}