<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsTip extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_tip', function($table)
        {
            $table->string('read_more', 50)->nullable();
            $table->string('link', 100)->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_tip', function($table)
        {
            $table->dropColumn('read_more');
            $table->dropColumn('link');
        });
    }
}
