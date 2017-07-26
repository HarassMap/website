<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsLocation3 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_location', function($table)
        {
            $table->dropColumn('region');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_location', function($table)
        {
            $table->string('region', 255);
        });
    }
}
