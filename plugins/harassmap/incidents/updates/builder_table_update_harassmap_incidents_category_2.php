<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsCategory2 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_category', function($table)
        {
            $table->dropColumn('color');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_category', function($table)
        {
            $table->string('color', 20);
        });
    }
}
