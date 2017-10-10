<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsDomain16 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->dropColumn('url');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->string('url', 255)->nullable();
        });
    }
}
