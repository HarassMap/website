<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsIncident7 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_incident', function($table)
        {
            $table->boolean('approved')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_incident', function($table)
        {
            $table->dropColumn('approved');
        });
    }
}
