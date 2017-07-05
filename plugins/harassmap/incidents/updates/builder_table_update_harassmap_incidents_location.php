<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsLocation extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_location', function($table)
        {
            $table->integer('incident_id')->unsigned();

            $table->foreign('incident_id')->references('id')->on('harassmap_incidents_incident');
        });

        Schema::table('harassmap_incidents_incident', function($table)
        {
            $table->dropForeign('harassmap_incidents_incident_location_id_foreign');
            $table->dropColumn('location_id');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_location', function($table)
        {
            $table->dropForeign('harassmap_incidents_location_incident_id_foreign');
            $table->dropColumn('incident_id');
        });

        Schema::table('harassmap_incidents_incident', function($table)
        {
            $table->integer('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('harassmap_incidents_location');
        });
    }
}