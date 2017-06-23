<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsIncident extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_incident', function($table)
        {
            $table->integer('role_id')->unsigned();

            $table->foreign('role_id')->references('id')->on('harassmap_incidents_role');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_incident', function($table)
        {
            $table->dropColumn('role_id');
        });
    }
}