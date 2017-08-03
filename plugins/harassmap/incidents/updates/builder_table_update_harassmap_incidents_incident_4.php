<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsIncident4 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_incident', function($table)
        {
            $table->integer('support');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_incident', function($table)
        {
            $table->dropColumn('support');
        });
    }
}
