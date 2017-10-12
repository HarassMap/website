<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsDomain18 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->string('default_language', 10)->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->dropColumn('default_language');
        });
    }
}
