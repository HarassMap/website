<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsIncident9 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_incident', function($table)
        {
            $table->boolean('is_hidden')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_incident', function($table)
        {
            $table->dropColumn('is_hidden');
        });
    }
}