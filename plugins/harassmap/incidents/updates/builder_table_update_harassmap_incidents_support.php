<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsSupport extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_support', function($table)
        {
            $table->string('link');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_support', function($table)
        {
            $table->dropColumn('link');
        });
    }
}
